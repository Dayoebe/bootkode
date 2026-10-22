<?php

namespace App\Http\Controllers;

use App\Models\Marketplace\MarketplaceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketplaceDownloadController extends Controller
{
    public function __invoke(Request $request, MarketplaceOrder $order, int $file = 0)
    {
        abort_unless((int) $order->customer_id === (int) $request->user()->id, 403);
        abort_unless($order->isPaid() && $order->item?->is_digital, 404);
        $asset = $order->item->files[$file] ?? null;
        $path = is_array($asset) ? ($asset['path'] ?? '') : '';
        abort_unless(is_string($path) && !str_contains($path, '..')
            && (str_starts_with($path, 'private/marketplace/files/') || str_starts_with($path, 'marketplace/files/')), 404);
        $disk = str_starts_with($path, 'private/') ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path, basename($asset['name'] ?? $path), [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
