<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class OfflineContentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('offline-content', function ($app) {
            return new \App\Services\OfflineContentService();
        });
    }

    // public function boot()
    // {
    //     Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
    //         return URL::temporarySignedRoute(
    //             'offline.content',
    //             $expiration,
    //             array_merge($options, ['path' => $path])
    //         );
    //     });
    // }
}