<?php

namespace App\Http\Controllers;

use App\Services\DashboardSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardSearchController extends Controller
{
    public function suggest(Request $request, DashboardSearchService $search): JsonResponse
    {
        return response()->json(
            $search->search($request->query('q'), $request->user(), 5)
        );
    }
}
