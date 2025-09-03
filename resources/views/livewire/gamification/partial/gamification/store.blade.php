@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                <i class="fas fa-store mr-3"></i>Reward Store
            </h1>
            <p class="text-gray-300 mt-2">Spend your earned coins and gems on cool rewards!</p>
        </div>

        <!-- User Currency Display -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl p-4 text-center">
                <i class="fas fa-coins text-2xl mb-2"></i>
                <div class="text-xs opacity-80">COINS</div>
                <div class="text-2xl font-bold">{{ number_format($userStats['coins'] ?? 0) }}</div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-4 text-center">
                <i class="fas fa-gem text-2xl mb-2"></i>
                <div class="text-xs opacity-80">GEMS</div>
                <div class="text-2xl font-bold">{{ $userStats['gems'] ?? 0 }}</div>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-4 text-center">
                <i class="fas fa-trophy text-2xl mb-2"></i>
                <div class="text-xs opacity-80">LEVEL</div>
                <div class="text-2xl font-bold">{{ $userStats['level'] ?? 1 }}</div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-center">
                <i class="fas fa-star text-2xl mb-2"></i>
                <div class="text-xs opacity-80">POINTS</div>
                <div class="text-lg font-bold">{{ number_format($userStats['total_points'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Store Items by Category -->
        @foreach($storeItems as $itemType => $items)
        <div class="mb-12">
            <h2 class="text-3xl font-bold mb-6 capitalize text-purple-400">
                <i class="fas fa-{{ $itemType === 'avatar' ? 'user' : ($itemType === 'theme' ? 'palette' : ($itemType === 'boost' ? 'rocket' : 'star')) }} mr-2"></i>
                {{ str_replace('_', ' ', $itemType) }}s
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($items as $item)
                <div class="store-item bg-black/30 rounded-xl p-6 backdrop-blur-sm border border-white/10 hover:border-purple-400 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center">
                        <div class="text-4xl mb-4 text-purple-400">
                            <i class="{{ $item->icon }}"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $item->name }}</h3>
                        <p class="text-gray-300 text-sm mb-4">{{ $item->description }}</p>
                        
                        <!-- Requirements -->
                        @if($item->required_level > 1)
                        <div class="text-xs text-yellow-400 mb-2">
                            <i class="fas fa-lock mr-1"></i>Requires Level {{ $item->required_level }}
                        </div>
                        @endif

                        <!-- Price Display -->
                        <div class="flex justify-center space-x-4 mb-4">
                            @if($item->cost_coins > 0)
                            <div class="bg-yellow-500/20 px-3 py-1 rounded-lg">
                                <span class="text-yellow-400">
                                    <i class="fas fa-coins"></i> {{ number_format($item->cost_coins) }}
                                </span>
                            </div>
                            @endif
                            @if($item->cost_gems > 0)
                            <div class="bg-purple-500/20 px-3 py-1 rounded-lg">
                                <span class="text-purple-400">
                                    <i class="fas fa-gem"></i> {{ $item->cost_gems }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Limited Time Badge -->
                        @if($item->is_limited_time)
                        <div class="bg-red-500/20 text-red-400 text-xs px-2 py-1 rounded-full mb-3">
                            <i class="fas fa-clock"></i> Limited Time
                        </div>
                        @endif

                        @php
                            $canPurchase = $item->canUserPurchase(auth()->user());
                            $alreadyOwned = in_array($item->id, $userPurchases);
                        @endphp

                        <!-- Purchase Button -->
                        @if($alreadyOwned)
                            <button class="w-full bg-green-600 px-4 py-3 rounded-lg font-medium cursor-not-allowed" disabled>
                                <i class="fas fa-check mr-2"></i>Owned
                            </button>
                        @elseif($canPurchase)
                            <form action="{{ route('gamification.purchase', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-4 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium">
                                    <i class="fas fa-shopping-cart mr-2"></i>Purchase
                                </button>
                            </form>
                        @else
                            <button class="w-full bg-gray-600 px-4 py-3 rounded-lg font-medium cursor-not-allowed" disabled>
                                <i class="fas fa-lock mr-2"></i>
                                @if(auth()->user()->gamificationData && auth()->user()->gamificationData->level < $item->required_level)
                                    Level {{ $item->required_level }} Required
                                @elseif($item->cost_coins > 0 && auth()->user()->gamificationData && auth()->user()->gamificationData->coins < $item->cost_coins)
                                    Not Enough Coins
                                @elseif($item->cost_gems > 0 && auth()->user()->gamificationData && auth()->user()->gamificationData->gems < $item->cost_gems)
                                    Not Enough Gems
                                @else
                                    Locked
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        @if($storeItems->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-store text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-400 mb-2">Store is empty!</h3>
            <p class="text-gray-500">Check back later for new items.</p>
        </div>
        @endif
    </div>
</div>

<style>
.store-item {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
}
</style>
@endsection