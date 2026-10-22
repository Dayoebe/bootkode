<svg xmlns="http://www.w3.org/2000/svg" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" role="img" aria-labelledby="title desc">
    <title id="title">BootKode — Learn. Build. Grow.</title>
    <desc id="desc">Explore practical technology learning. Referral code {{ $affiliate->referral_code }}.</desc>
    <rect width="100%" height="100%" rx="8" fill="#0f172a" />
    @if($size === 'medium')
        <text x="24" y="58" fill="#8fe388" font-family="sans-serif" font-size="30" font-weight="700">BootKode</text>
        <text x="24" y="102" fill="#ffffff" font-family="sans-serif" font-size="22">Learn. Build. Grow.</text>
        <text x="24" y="150" fill="#ffffff" font-family="sans-serif" font-size="15">Practical technology learning</text>
        <text x="24" y="204" fill="#8fe388" font-family="sans-serif" font-size="14">Referral: {{ $affiliate->referral_code }}</text>
    @else
        <text x="20" y="35" fill="#8fe388" font-family="sans-serif" font-size="23" font-weight="700">BootKode</text>
        <text x="20" y="62" fill="#ffffff" font-family="sans-serif" font-size="16">Learn. Build. Grow.</text>
        <text x="{{ $size === 'large' ? 400 : 20 }}" y="{{ $size === 'large' ? 52 : 85 }}" fill="#8fe388" font-family="sans-serif" font-size="14">Referral: {{ $affiliate->referral_code }}</text>
    @endif
</svg>
