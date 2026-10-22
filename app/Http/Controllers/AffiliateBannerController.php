<?php

namespace App\Http\Controllers;

use App\Models\Marketplace\Affiliate;

class AffiliateBannerController extends Controller
{
    public function __invoke(string $size, string $code)
    {
        $sizes = ['large' => [728, 90], 'medium' => [300, 250], 'small' => [320, 100]];
        abort_unless(isset($sizes[$size]), 404);
        $affiliate = Affiliate::active()->where('referral_code', $code)->firstOrFail();
        return response()->view('affiliate.banner', ['affiliate' => $affiliate, 'size' => $size, 'width' => $sizes[$size][0], 'height' => $sizes[$size][1]], 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8', 'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
