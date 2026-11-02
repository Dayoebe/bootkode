<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: ['webhook/paystack']);
        $middleware->web(append: [
            \App\Http\Middleware\RecordProductionObservability::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $exception) {
            try {
                app(\App\Services\ObservabilityService::class)->recordException($exception, request());
            } catch (\Throwable) {
                //
            }
            if (app()->environment('production')) {
                \Illuminate\Support\Facades\Log::error('Unhandled application exception.', [
                    'exception_type' => $exception::class,
                    'file' => basename($exception->getFile()),
                    'line' => $exception->getLine(),
                    'route' => request()?->route()?->getName(),
                ]);

                // Default exception strings can contain SQL bindings, request URLs or credentials.
                return false;
            }
        });
    })->create();
