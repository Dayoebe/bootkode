<?php
// app/Http/Controllers/MarketplaceController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Core\User;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MarketplaceController extends Controller
{
    // Item Management
    public function store(Request $request)
    {
        $this->authorize('create', MarketplaceItem::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100',
            'short_description' => 'required|string|max:160',
            'type' => ['required', Rule::in(array_keys(MarketplaceItem::TYPES))],
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|lt:price',
            'is_digital' => 'boolean',
            'categories' => 'required|array|min:1',
            'tags' => 'nullable|array|min:1',
            'thumbnail' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
            'files.*' => 'nullable|file|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'keywords' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('marketplace/thumbnails', 'public');
            }

            $imagesPaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagesPaths[] = [
                        'path' => $image->store('marketplace/images', 'public'),
                        'name' => $image->getClientOriginalName(),
                        'size' => $image->getSize(),
                    ];
                }
            }

            $filesPaths = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filesPaths[] = [
                        'path' => $file->store('private/marketplace/files', 'local'),
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            $item = MarketplaceItem::create([
                'vendor_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'short_description' => $validated['short_description'],
                'type' => $validated['type'],
                'price' => $validated['price'],
                'discount_price' => $validated['discount_price'],
                'is_digital' => $validated['is_digital'] ?? true,
                'thumbnail' => $thumbnailPath,
                'images' => $imagesPaths,
                'files' => $filesPaths,
                'categories' => $validated['categories'],
                'tags' => $validated['tags'],
                'meta_title' => $validated['meta_title'] ?? $validated['title'],
                'meta_description' => $validated['meta_description'] ?? $validated['short_description'],
                'keywords' => $validated['keywords'],
                'duration_minutes' => $validated['duration_minutes'],
                'status' => MarketplaceItem::STATUS_DRAFT,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully!',
                'item' => $item->load('vendor'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create item: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, MarketplaceItem $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100',
            'short_description' => 'required|string|max:160',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|lt:price',
            'categories' => 'required|array|min:1',
            'tags' => 'nullable|array|min:1',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'keywords' => 'nullable|string',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully!',
            'item' => $item->fresh(),
        ]);
    }

    public function destroy(MarketplaceItem $item)
    {
        $this->authorize('delete', $item);

        // Check if item has any orders
        if ($item->orders()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete item with existing orders.',
            ], 400);
        }

        // Preserve assets while the listing is soft deleted.
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully!',
        ]);
    }

    public function submitForReview(MarketplaceItem $item)
    {
        $this->authorize('update', $item);

        if (!$item->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft items can be submitted for review.',
            ], 400);
        }

        $item->submitForReview();

        return response()->json([
            'success' => true,
            'message' => 'Item submitted for review successfully!',
        ]);
    }

    // Admin Actions
    public function approve(Request $request, MarketplaceItem $item)
    {
        $this->authorize('approve', $item);

        $item->approve();

        return response()->json([
            'success' => true,
            'message' => 'Item approved successfully!',
        ]);
    }

    public function reject(Request $request, MarketplaceItem $item)
    {
        $this->authorize('approve', $item);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $item->reject($validated['reason']);

        return response()->json([
            'success' => true,
            'message' => 'Item rejected successfully!',
        ]);
    }

    public function suspend(Request $request, MarketplaceItem $item)
    {
        $this->authorize('approve', $item);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $item->suspend($validated['reason'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Item suspended successfully!',
        ]);
    }

    // Cart & Checkout
    public function addToCart(Request $request, MarketplaceItem $item)
    {
        if (!$item->isPublished()) {
            return response()->json([
                'success' => false,
                'message' => 'This item is not available for purchase.',
            ], 400);
        }

        // Check if user already owns this item
        $existingOrder = MarketplaceOrder::where('customer_id', auth()->id())
            ->where('item_id', $item->id)
            ->where('payment_status', MarketplaceOrder::PAYMENT_STATUS_PAID)
            ->exists();

        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'You already own this item.',
            ], 400);
        }

        // Add to session cart (you could also use a dedicated Cart model)
        $cart = session()->get('marketplace_cart', []);
        
        if (!isset($cart[$item->id])) {
            $cart[$item->id] = [
                'item_id' => $item->id,
                'title' => $item->title,
                'price' => $item->getEffectivePrice(),
                'thumbnail' => $item->getPrimaryImage(),
                'vendor_name' => $item->vendor->name,
            ];
        }

        session()->put('marketplace_cart', $cart);
        session()->forget('marketplace_checkout_key');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart!',
            'cart_count' => count($cart),
        ]);
    }

    public function removeFromCart(MarketplaceItem $item)
    {
        $cart = session()->get('marketplace_cart', []);
        unset($cart[$item->id]);
        session()->put('marketplace_cart', $cart);
        session()->forget('marketplace_checkout_key');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!',
            'cart_count' => count($cart),
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:wallet', 'items' => 'required|array|min:1|max:30',
            'items.*.item_id' => 'required|integer|exists:marketplace_items,id',
            'items.*.quantity' => 'sometimes|integer|min:1|max:10',
        ]);
        $key = session('marketplace_checkout_key');
        if (!$key) {
            $key = (string) \Illuminate\Support\Str::uuid();
            session()->put('marketplace_checkout_key', $key);
        }
        try {
            $orders = app(\App\Services\MarketplaceService::class)->checkoutWallet($request->user(), $validated['items'], $key);
            session()->forget('marketplace_cart');
            return response()->json(['success' => true, 'orders' => collect($orders)->pluck('order_number'), 'redirect' => route('marketplace.purchases')]);
        } catch (\Throwable $exception) {
            return response()->json(['success' => false, 'message' => $exception instanceof \DomainException ? $exception->getMessage() : 'The purchase could not be completed.'], 422);
        }
    }

    // Order Management
    public function fulfillOrder(Request $request, MarketplaceOrder $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'delivery_notes' => 'nullable|string|max:500',
            'tracking_info' => 'nullable|string|max:255',
        ]);

        if (!$order->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Order must be paid before fulfillment.',
            ], 400);
        }

        $deliveryDetails = array_filter([
            'fulfilled_by' => auth()->user()->name,
            'fulfilled_at' => now(),
            'notes' => $validated['delivery_notes'] ?? null,
            'tracking_info' => $validated['tracking_info'] ?? null,
        ]);

        $order->complete($deliveryDetails);

        return response()->json([
            'success' => true,
            'message' => 'Order fulfilled successfully!',
        ]);
    }

    public function refund(Request $request, MarketplaceOrder $order)
    {
        $this->authorize('refund', $order);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'amount' => 'nullable|numeric|min:0|max:' . $order->total_amount,
        ]);

        $refundAmount = $validated['amount'] ?? $order->total_amount;
        
        try {
            $order->refund($refundAmount, $validated['reason']);
        } catch (\DomainException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Refund processed successfully!',
        ]);
    }

    // Public Views
    public function show($slug)
    {
        $item = MarketplaceItem::where('slug', $slug)
            ->published()
            ->with(['vendor', 'reviews' => fn ($query) => $query->approved()->with('user:id,name')])
            ->firstOrFail();

        $item->incrementViews();

        $relatedItems = MarketplaceItem::published()
            ->where('type', $item->type)
            ->where('id', '!=', $item->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('marketplace.item', compact('item', 'relatedItems'));
    }

    public function incrementViews(MarketplaceItem $item)
    {
        $item->incrementViews();
        
        return response()->json(['success' => true]);
    }

    // Payment Callbacks
    public function paymentCallback(Request $request)
    {
        // Implement Paystack webhook handling
        $reference = $request->input('reference');
        
        // Verify payment with Paystack
        // Update order status accordingly
        
        return response()->json(['success' => true]);
    }

    public function paymentSuccess(Request $request)
    {
        $reference = $request->input('reference');
        
        // Find and update orders
        // Redirect to success page
        
        return redirect()->route('marketplace.purchases')
            ->with('message', 'Payment successful! Your items are now available.');
    }

    public function paymentFailed(Request $request)
    {
        return redirect()->route('marketplace.checkout')
            ->with('error', 'Payment failed. Please try again.');
    }

    // Helper Methods
    private function initiatePaystackPayment($orders, $amount)
    {
        // Implement Paystack payment initialization
        // Return payment URL
        return 'https://checkout.paystack.com/...';
    }

    public function approveVendor(User $user)
    {
        $this->authorize('manage-vendors');

        // Logic to approve vendor application
        // This might involve updating user roles or creating vendor profiles

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved successfully!',
        ]);
    }
}