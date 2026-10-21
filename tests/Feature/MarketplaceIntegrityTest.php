<?php

namespace Tests\Feature;

use App\Livewire\Marketplace\MarketplaceCenter;
use App\Livewire\Marketplace\Partial\MarketplaceAdmin;
use App\Livewire\Marketplace\Partial\MarketplaceBrowse;
use App\Livewire\Marketplace\Partial\MarketplaceShopping;
use App\Livewire\Marketplace\Partial\MarketplaceSystem;
use App\Models\Core\User;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\Wallet;
use App\Services\CommercialReadinessService;
use App\Services\MarketplaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MarketplaceIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        Role::findOrCreate('super_admin', 'web');
        $admin = User::factory()->create(['role' => 'super_admin']);
        Role::findOrCreate('instructor', 'web');
        $vendor = User::factory()->create(['role' => 'instructor']);
        $customer = User::factory()->create();
        $item = MarketplaceItem::withoutSyncingToSearch(fn () => MarketplaceItem::create([
            'vendor_id' => $vendor->id, 'title' => 'Private test resource', 'description' => 'Test-only resource',
            'type' => 'resource', 'status' => 'approved', 'price' => 100, 'currency' => 'NGN', 'is_digital' => true,
        ]));
        $wallet = Wallet::getOrCreateWallet($customer->id);
        $wallet->credit(1000, 'funding', 'Test fixture');
        config(['marketplace.platform_user_id' => $admin->id]);
        $this->actingAs($customer);

        return [$admin, $vendor, $customer, $item, $wallet];
    }

    public function test_nested_admin_components_and_forged_tabs_are_protected(): void
    {
        $this->actingAs(User::factory()->create());
        foreach ([MarketplaceAdmin::class, MarketplaceSystem::class, \App\Livewire\Marketplace\Partial\MarketplaceVendor::class, \App\Livewire\Marketplace\Partial\MarketplaceBusiness::class, \App\Livewire\Marketplace\Partial\MarketplaceContent::class] as $component) {
            Livewire::test($component)->assertForbidden();
        }
        Livewire::test(MarketplaceCenter::class)->set('activeTab', 'admin')->assertForbidden();
        $this->get(route('marketplace.orders'))->assertForbidden();
        Livewire::test(MarketplaceBrowse::class)->call('createCategory')->assertForbidden();
    }

    public function test_checkout_uses_server_prices_and_records_balanced_idempotent_payments(): void
    {
        [$admin, $vendor, $customer, $item, $wallet] = $this->fixture();
        $service = app(MarketplaceService::class);
        $key = (string) Str::uuid();
        $orders = $service->checkoutWallet($customer, [['item_id' => $item->id, 'price' => 0]], $key);
        $retry = $service->checkoutWallet($customer, [['item_id' => $item->id]], $key);
        $this->assertSame($orders[0]->id, $retry[0]->id);
        $this->assertDatabaseCount('marketplace_orders', 1);
        $this->assertEquals(900, $wallet->fresh()->balance);
        $this->assertEquals(80, Wallet::getOrCreateWallet($vendor->id, 'instructor')->balance);
        $this->assertEquals(20, Wallet::getOrCreateWallet($admin->id, 'platform')->balance);
        $this->assertSame(1, $item->fresh()->sales_count);
        $this->assertDatabaseHas('commercial_documents', ['marketplace_order_id' => $orders[0]->id, 'status' => 'paid', 'amount_paid' => 100]);
        $orders[0]->processVendorPayment();
        $this->assertEquals(80, Wallet::getOrCreateWallet($vendor->id, 'instructor')->fresh()->balance);
    }

    public function test_invoice_failure_rolls_back_the_order_all_ledgers_and_balances(): void
    {
        [, , $customer, $item, $wallet] = $this->fixture();
        $this->partialMock(CommercialReadinessService::class)->shouldReceive('issueInvoiceForOrder')->once()->andThrow(new \RuntimeException('Forced invoice failure'));
        try {
            app(MarketplaceService::class)->checkoutWallet($customer, [['item_id' => $item->id]], (string) Str::uuid());
            $this->fail('Invoice failure did not roll back checkout');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Forced invoice failure', $exception->getMessage());
        }
        $this->assertDatabaseCount('marketplace_orders', 0);
        $this->assertDatabaseCount('wallet_transactions', 1);
        $this->assertEquals(1000, $wallet->fresh()->balance);
        $this->assertSame(0, $item->fresh()->sales_count);
    }

    public function test_card_checkout_does_not_create_orders_or_report_paid_success(): void
    {
        [, , , $item, $wallet] = $this->fixture();
        session()->put('marketplace_cart', [$item->id => ['item_id' => $item->id, 'quantity' => 1]]);
        Livewire::test(MarketplaceShopping::class)->set('paymentMethod', 'paystack')->call('checkout')->assertHasErrors('paymentMethod');
        $this->assertDatabaseCount('marketplace_orders', 0);
        $this->assertEquals(1000, $wallet->fresh()->balance);
    }

    public function test_purchase_search_cannot_return_another_customers_order_number(): void
    {
        [, , $customer, $item] = $this->fixture();
        $other = User::factory()->create();
        $order = MarketplaceOrder::create(['customer_id' => $other->id, 'vendor_id' => $item->vendor_id, 'item_id' => $item->id,
            'order_number' => 'PRIVATE-ORDER-SEARCH', 'item_price' => 100, 'total_amount' => 100, 'platform_commission_rate' => 20]);
        Livewire::actingAs($customer)->test(MarketplaceShopping::class)->set('currentView', 'purchases')
            ->set('search', $order->order_number)->assertDontSee($order->order_number);
    }

    public function test_refund_replays_cannot_mint_credit_and_reverse_the_original_payouts(): void
    {
        [$admin, $vendor, $customer, $item, $wallet] = $this->fixture();
        $order = app(MarketplaceService::class)->checkoutWallet($customer, [['item_id' => $item->id]], (string) Str::uuid())[0];
        $stale = $order->fresh();
        $this->actingAs($admin);
        $order->refund();
        $stale->refund();
        $this->assertEquals(1000, $wallet->fresh()->balance);
        $this->assertEquals(0, Wallet::getOrCreateWallet($vendor->id, 'instructor')->fresh()->balance);
        $this->assertEquals(0, Wallet::getOrCreateWallet($admin->id, 'platform')->fresh()->balance);
        $this->assertDatabaseHas('commercial_documents', ['marketplace_order_id' => $order->id, 'status' => 'refunded', 'amount_refunded' => 100]);
    }

    public function test_refund_without_recoverable_payout_funds_rolls_back(): void
    {
        [$admin, $vendor, $customer, $item, $wallet] = $this->fixture();
        $order = app(MarketplaceService::class)->checkoutWallet($customer, [['item_id' => $item->id]], (string) Str::uuid())[0];
        Wallet::getOrCreateWallet($vendor->id, 'instructor')->debit(80, 'withdrawal', 'Test fixture');
        $this->actingAs($admin);
        try {
            $order->refund();
            $this->fail('Unfunded refund was accepted');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('Insufficient', $exception->getMessage());
        }
        $this->assertTrue($order->fresh()->isPaid());
        $this->assertEquals(900, $wallet->fresh()->balance);
    }

    public function test_paid_status_requires_an_exact_linked_wallet_debit(): void
    {
        [, , $customer, $item] = $this->fixture();
        $order = MarketplaceOrder::create(['customer_id' => $customer->id, 'vendor_id' => $item->vendor_id, 'item_id' => $item->id,
            'item_price' => 100, 'total_amount' => 100, 'payment_status' => 'unpaid', 'platform_commission_rate' => 20]);
        try {
            $order->markAsPaid(['status' => 'success', 'wallet_transaction_id' => 1]);
            $this->fail('Unverified debit was accepted');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('matching verified wallet debit', $exception->getMessage());
        }
        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }

    public function test_private_download_requires_paid_ownership_and_hides_asset_paths_from_discovery(): void
    {
        Storage::fake('local');
        [, , $customer, $item] = $this->fixture();
        $item->update(['files' => [['path' => 'private/marketplace/files/fixture.pdf', 'name' => 'Resource.pdf']]]);
        Storage::disk('local')->put('private/marketplace/files/fixture.pdf', '%PDF-test');
        $this->assertArrayNotHasKey('files', $item->toArray());
        $order = app(MarketplaceService::class)->checkoutWallet($customer, [['item_id' => $item->id]], (string) Str::uuid())[0];
        MarketplaceItem::withoutSyncingToSearch(fn () => $item->delete());
        $this->get(route('marketplace.order.download', $order))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->actingAs(User::factory()->create())->get(route('marketplace.order.download', $order))->assertForbidden();
    }
    public function test_small_payments_split_in_minor_units_without_creating_money(): void
    {
        [$admin, $vendor, $customer, $item, $wallet] = $this->fixture();
        $item->update(['price' => 0.01]);
        \Illuminate\Support\Facades\Cache::put('marketplace_settings', ['platform_commission' => 50]);
        try {
            $order = app(MarketplaceService::class)->checkoutWallet($customer, [['item_id' => $item->id]], (string) Str::uuid())[0];
            $this->assertSame(1, (int) round(((float) $order->platform_commission + (float) $order->vendor_earning) * 100));
            $this->assertEquals(999.99, $wallet->fresh()->balance);
            $this->assertEquals(0.01, $order->walletTransactions()->where('type', 'credit')->sum('amount'));
        } finally {
            \Illuminate\Support\Facades\Cache::forget('marketplace_settings');
        }
    }

    public function test_a_foreign_currency_settlement_wallet_rolls_back_checkout(): void
    {
        [, $vendor, $customer, $item, $wallet] = $this->fixture();
        Wallet::getOrCreateWallet($vendor->id, Wallet::TYPE_INSTRUCTOR)->update(['currency' => 'USD']);
        try {
            app(MarketplaceService::class)->checkoutWallet($customer, [['item_id' => $item->id]], (string) Str::uuid());
            $this->fail('Mismatched settlement currency was accepted');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('currency', $exception->getMessage());
        }
        $this->assertEquals(1000, $wallet->fresh()->balance);
        $this->assertDatabaseCount('marketplace_orders', 0);
        $this->assertDatabaseCount('wallet_transactions', 1);
    }

    public function test_public_product_payload_contains_only_approved_reviews_and_hides_files(): void
    {
        [, , $customer, $item] = $this->fixture();
        $item->update(['files' => [['path' => 'private/marketplace/files/private.pdf']]]);
        foreach ([false, true] as $approved) {
            $item->reviews()->create(['user_id' => $customer->id, 'rating' => 4, 'comment' => $approved ? 'Visible feedback' : 'PRIVATE moderation note', 'is_approved' => $approved]);
        }
        Livewire::test(\App\Livewire\Marketplace\PublicMarketplace::class)->call('viewProduct', $item->id)
            ->assertSet('selectedProduct.reviews', fn ($reviews) => $reviews->count() === 1)
            ->assertDontSee('PRIVATE moderation note');
        $this->assertArrayNotHasKey('files', $item->fresh()->toArray());
    }

    public function test_vendor_uploads_are_persisted_privately_and_survive_a_metadata_edit(): void
    {
        [, $vendor] = $this->fixture();
        Storage::fake('local');
        Storage::fake('public');
        $category = \App\Models\Marketplace\MarketplaceCategory::create(['name' => 'Fixtures', 'slug' => 'fixtures', 'is_active' => true]);
        $component = Livewire::actingAs($vendor)->test(\App\Livewire\Marketplace\Partial\MarketplaceVendor::class)
            ->set('title', 'Uploaded fixture')->set('description', str_repeat('Real test content. ', 8))
            ->set('short_description', 'Test upload')->set('type', 'resource')->set('price', 100)
            ->set('selectedCategories', [$category->id])
            ->set('files', [\Illuminate\Http\UploadedFile::fake()->create('private-guide.pdf', 10, 'application/pdf')])
            ->call('save')->assertHasNoErrors();
        $item = MarketplaceItem::where('title', 'Uploaded fixture')->firstOrFail();
        $path = $item->files[0]['path'];
        $this->assertStringStartsWith('private/marketplace/files/', $path);
        Storage::disk('local')->assertExists($path);
        Storage::disk('public')->assertMissing($path);
        $component->call('editItem', $item->id)->set('title', 'Edited upload')->call('save')->assertHasNoErrors();
        $this->assertSame($path, $item->fresh()->files[0]['path']);
        Storage::disk('local')->assertExists($path);
    }

    public function test_public_selection_rechecks_publication_after_livewire_hydration(): void
    {
        [, , , $item] = $this->fixture();
        $component = Livewire::test(\App\Livewire\Marketplace\PublicMarketplace::class)->call('viewProduct', $item->id);
        $item->update(['status' => MarketplaceItem::STATUS_DRAFT]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $component->call('$refresh');
    }

    public function test_checkout_confirmation_survives_subsequent_livewire_updates(): void
    {
        [, , $customer, $item] = $this->fixture();
        session()->put('marketplace_cart', [['item_id' => $item->id, 'quantity' => 1]]);
        Livewire::actingAs($customer)->test(MarketplaceShopping::class)->call('checkout')
            ->assertSee('Wallet purchase completed.')->call('showPurchases')->assertSee('Wallet purchase completed.');
    }

    public function test_customer_purchase_view_excludes_internal_administration_notes(): void
    {
        [, , $customer, $item] = $this->fixture();
        $order = app(MarketplaceService::class)->checkoutWallet($customer, [['item_id' => $item->id]], (string) Str::uuid())[0];
        $order->update(['admin_notes' => 'PRIVATE internal administration note', 'customer_notes' => 'Customer supplied note']);
        Livewire::actingAs($customer)->test(MarketplaceShopping::class)->call('showPurchases')
            ->assertDontSee('PRIVATE internal administration note')->assertSee('Customer supplied note');
    }

}
