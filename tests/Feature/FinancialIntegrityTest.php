<?php

namespace Tests\Feature;

use App\Livewire\StudentManagement\CourseCatalog;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Marketplace\PaystackTransaction;
use App\Services\CommercialReadinessService;
use App\Services\PaystackService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function funding(): array
    {
        $wallet = User::factory()->create()->getOrCreateWallet();
        $payment = PaystackTransaction::create([
            'reference' => 'funding-fixture', 'amount' => 1000, 'currency' => 'NGN',
            'status' => 'pending', 'customer_email' => $wallet->user->email,
            'transaction_type' => PaystackTransaction::TYPE_WALLET_FUNDING,
            'transactionable_type' => $wallet->getMorphClass(), 'transactionable_id' => $wallet->id,
        ]);
        config(['services.paystack.secret_key' => 'sk_test_fixture']);
        return [$wallet, $payment];
    }

    private function gateway(array $overrides = []): void
    {
        Http::preventStrayRequests();
        Http::fake(['api.paystack.co/transaction/verify/*' => Http::response(['data' => array_merge([
            'status' => 'success', 'reference' => 'funding-fixture', 'amount' => 100000,
            'currency' => 'NGN', 'paid_at' => now()->toIso8601String(),
        ], $overrides)])]);
    }

    public function test_verified_funding_is_idempotent_and_returns_amount_on_replay(): void
    {
        [$wallet] = $this->funding();
        $this->gateway();
        $service = app(PaystackService::class);
        $this->assertTrue($service->processWalletFunding('funding-fixture')['success']);
        $replay = $service->processWalletFunding('funding-fixture');
        $this->assertTrue($replay['success']);
        $this->assertSame(1000.0, $replay['amount']);
        $this->assertSame('1000.00', $wallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 1);
        $this->assertDatabaseCount('commercial_documents', 1);
    }

    public function test_gateway_amount_currency_and_reference_must_match_local_intent(): void
    {
        [$wallet, $payment] = $this->funding();
        foreach ([['amount' => 1], ['currency' => 'USD'], ['reference' => 'other'], ['status' => 'failed']] as $change) {
            $this->gateway($change);
            $this->assertFalse(app(PaystackService::class)->processWalletFunding('funding-fixture')['success']);
        }
        $this->assertSame('0.00', $wallet->fresh()->balance);
        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_funding_rolls_back_balance_ledger_and_status_if_receipt_fails(): void
    {
        [$wallet, $payment] = $this->funding();
        $this->gateway();
        $this->mock(CommercialReadinessService::class)->shouldReceive('issueReceiptForTransaction')
            ->once()->andThrow(new \RuntimeException('Fixture failure'));
        $this->assertFalse(app(PaystackService::class)->processWalletFunding('funding-fixture')['success']);
        $this->assertSame('0.00', $wallet->fresh()->balance);
        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_wallet_uses_current_balance_and_rejects_negative_mutations(): void
    {
        [$wallet] = $this->funding();
        $stale = $wallet->fresh();
        $wallet->credit(100, 'funding', 'Fixture');
        $stale->credit(50, 'funding', 'Fixture');
        $this->assertSame('150.00', $wallet->fresh()->balance);
        try {
            $wallet->debit(-10, 'course_purchase', 'Invalid');
            $this->fail('Negative debit was accepted');
        } catch (\InvalidArgumentException) {
            $this->assertSame('150.00', $wallet->fresh()->balance);
        }
        $this->assertDatabaseCount('wallet_transactions', 2);
    }

    public function test_signed_webhook_is_public_and_replayed_delivery_does_not_double_credit(): void
    {
        [$wallet] = $this->funding();
        $this->gateway();
        $payload = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'funding-fixture']]);
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'sk_test_fixture')];
        $this->call('POST', '/webhook/paystack', [], [], [], $headers, $payload)->assertOk();
        $this->call('POST', '/webhook/paystack', [], [], [], $headers, $payload)->assertOk();
        $this->assertSame('1000.00', $wallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 1);
    }

    public function test_missing_secret_and_invalid_webhook_signature_fail_closed(): void
    {
        config(['services.paystack.secret_key' => null]);
        $this->postJson('/webhook/paystack', ['event' => 'charge.success'])->assertStatus(503);
        config(['services.paystack.secret_key' => 'sk_test_fixture']);
        $this->postJson('/webhook/paystack', ['event' => 'charge.success'])->assertStatus(400);
    }

    public function test_paid_enrollment_cannot_bypass_checkout_and_purchase_is_idempotent(): void
    {
        $learner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create([
            'title' => 'Fixture course', 'slug' => 'fixture-course', 'instructor_id' => User::factory()->create()->id,
            'is_published' => true, 'is_approved' => true, 'price' => 100, 'is_free' => false,
        ]));
        Livewire::actingAs($learner)->test(CourseCatalog::class)->call('enroll', $course->id)
            ->assertRedirect(route('course.checkout', $course->id));
        $this->assertDatabaseCount('course_enrollments', 0);
        $wallet = $learner->getOrCreateWallet();
        $level = DB::transactionLevel();
        $this->assertFalse(app(WalletService::class)->purchaseCourse($learner, $course)['success']);
        $this->assertSame($level, DB::transactionLevel());
        $wallet->credit(200, 'funding', 'Fixture');
        $this->assertTrue(app(WalletService::class)->purchaseCourse($learner, $course)['success']);
        $this->assertTrue(app(WalletService::class)->purchaseCourse($learner, $course)['success']);
        $this->assertSame('100.00', $wallet->fresh()->balance);
        $this->assertDatabaseCount('course_enrollments', 1);
        $this->assertDatabaseCount('course_user', 1);
        $this->assertSame('80.00', $course->instructor->getOrCreateInstructorWallet()->fresh()->balance);
    }

    public function test_unpublished_course_cannot_be_enrolled_by_livewire_id(): void
    {
        $learner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create([
            'title' => 'Private fixture', 'slug' => 'private-fixture', 'instructor_id' => $learner->id,
            'is_published' => false, 'is_approved' => true,
        ]));
        try {
            Livewire::actingAs($learner)->test(CourseCatalog::class)->call('enroll', $course->id);
            $this->fail('Unpublished course was accepted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            $this->assertDatabaseCount('course_enrollments', 0);
        }
        $this->assertDatabaseCount('course_enrollments', 0);
    }
}
