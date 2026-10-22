<?php

namespace Tests\Feature;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Marketplace\Affiliate;
use App\Models\Marketplace\Referral;
use App\Models\Marketplace\ReferralTransaction;
use App\Models\Marketplace\WalletTransaction;
use App\Services\AffiliateService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AffiliateIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $instructor = User::factory()->create();
        $promoter = User::factory()->create();
        $buyer = User::factory()->create(['referred_by' => $promoter->id]);
        $affiliate = Affiliate::create(['user_id' => $promoter->id, 'commission_rate' => 30, 'status' => 'active']);
        $referral = Referral::create(['affiliate_id' => $affiliate->id, 'referred_user_id' => $buyer->id, 'status' => 'pending']);
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => $instructor->id, 'title' => 'Affiliate fixture', 'slug' => 'affiliate-fixture', 'description' => 'Published fixture', 'price' => 100, 'is_free' => false, 'is_published' => true, 'is_approved' => true]));
        $buyer->getOrCreateWallet()->credit(1000, 'funding', 'Test funding');
        return [$buyer, $promoter, $affiliate, $referral, $course];
    }

    public function test_course_commission_is_backed_by_original_split_and_replay_safe(): void
    {
        [$buyer, $promoter, $affiliate, $referral, $course] = $this->fixture();
        $this->assertTrue(app(WalletService::class)->purchaseCourse($buyer, $course)['success']);
        $debit = $buyer->getOrCreateWallet()->transactions()->where('category', WalletTransaction::CATEGORY_COURSE_PURCHASE)->firstOrFail();
        $this->assertTrue(app(AffiliateService::class)->processCommission($buyer, $course, 100, $debit)['success']);
        $this->assertEquals(6, $promoter->getOrCreateWallet()->fresh()->balance);
        $this->assertSame(1, ReferralTransaction::count());
        $this->assertEquals(1, $referral->fresh()->courses_purchased);
        $this->assertSame($referral->id, ReferralTransaction::first()->referral->id);
        $this->assertSame(1, $affiliate->transactions()->count());
        $this->assertEquals(900, $buyer->getOrCreateWallet()->fresh()->balance);
    }

    public function test_wrong_buyer_or_amount_cannot_create_commission(): void
    {
        [$buyer, $promoter, , , $course] = $this->fixture();
        $debit = $buyer->getOrCreateWallet()->debit(100, WalletTransaction::CATEGORY_COURSE_PURCHASE, 'Malformed purchase', $course);
        $this->assertFalse(app(AffiliateService::class)->processCommission($buyer, $course, 999, $debit)['success']);
        $this->assertFalse(app(AffiliateService::class)->processCommission($buyer, $course, 100, $debit)['success']);
        $this->assertEquals(0, $promoter->getOrCreateWallet()->balance);
        $this->assertDatabaseCount('referral_transactions', 0);
    }

    public function test_small_course_split_never_allocates_more_than_debit(): void
    {
        $split = new \App\Models\Marketplace\RevenueSplit(['instructor_percentage' => 50, 'platform_percentage' => 50, 'is_active' => true]);
        $amounts = $split->calculateSplit(0.01);
        $this->assertEquals(0.01, $amounts['instructor_amount'] + $amounts['platform_amount']);
    }

    public function test_settings_actions_require_super_admin_and_validate_rates(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(\App\Livewire\Affiliate\Settings::class)->assertForbidden();
        Role::findOrCreate('super_admin', 'web');
        $this->actingAs(User::factory()->create(['role' => 'super_admin']));
        [, , $affiliate] = $this->fixture();
        Livewire::test(\App\Livewire\Affiliate\Settings::class)->call('updateCommissionRate', $affiliate->id, 101)->assertHasErrors('rate');
        $this->assertEquals(30, $affiliate->fresh()->commission_rate);
    }
    public function test_instructor_application_uses_configured_approval_and_is_replay_safe(): void
    {
        Role::findOrCreate('instructor', 'web');
        $user = User::factory()->create(['role' => 'instructor']);
        config(['affiliate.auto_approve_instructors' => false, 'affiliate.default_commission_rate' => 12]);
        $affiliate = $user->applyForAffiliate();
        $this->assertSame('inactive', $affiliate->status);
        $this->assertEquals(12, $affiliate->commission_rate);
        $this->assertNull($user->applyForAffiliate());
        $this->assertDatabaseCount('affiliates', 1);
    }

    public function test_public_banner_is_real_factual_svg_and_rejects_suspended_accounts(): void
    {
        [, , $affiliate] = $this->fixture();
        $url = route('affiliate.banner', ['size' => 'large', 'code' => $affiliate->referral_code]);
        $this->get($url)->assertOk()->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8')->assertSee('Learn. Build. Grow.')->assertDontSee('thousands');
        $affiliate->update(['status' => 'suspended']);
        $this->get($url)->assertNotFound();
    }

    public function test_referral_validation_does_not_disclose_account_or_financial_payload(): void
    {
        [$buyer, , $affiliate] = $this->fixture();
        $this->actingAs($buyer)->get(route('affiliate.api.validate-referral', $affiliate->referral_code))->assertOk()->assertJsonPath('valid', true)
            ->assertJsonMissingPath('affiliate')->assertJsonMissingPath('commission_rate');
    }

    public function test_course_revenue_excludes_affiliate_cost_and_unrelated_wallet_debits(): void
    {
        [$buyer, , , , $course] = $this->fixture();
        $service = app(WalletService::class);
        $this->assertTrue($service->purchaseCourse($buyer, $course)['success']);
        $buyer->getOrCreateWallet()->debit(10, WalletTransaction::CATEGORY_COURSE_PURCHASE, 'Unrelated payment');
        $stats = $service->getRevenueAnalytics();
        $this->assertEquals(100, $stats['total_course_sales']);
        $this->assertEquals(14, $stats['platform_commission']);
    }

    public function test_overview_report_contains_real_commissions_and_downloads_with_date_validation(): void
    {
        [$buyer, $promoter, , , $course] = $this->fixture();
        $this->assertTrue(app(WalletService::class)->purchaseCourse($buyer, $course)['success']);
        Livewire::actingAs($promoter)->test(\App\Livewire\Affiliate\CommissionReports::class)
            ->assertSee('Affiliate fixture')->assertSee('₦6.00')->call('exportReport')
            ->assertFileDownloaded('affiliate-report-overview-'.now()->format('Y-m-d').'.csv');
    }

}
