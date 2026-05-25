<?php

namespace App\Http\Middleware;

use App\Services\ObservabilityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordProductionObservability
{
    public function __construct(private ObservabilityService $observability)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        $response = $next($request);

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
        $this->observability->observeResponse($request, $response, $durationMs);

        return $response;
    }
}
