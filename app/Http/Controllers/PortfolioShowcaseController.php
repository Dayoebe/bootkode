<?php

namespace App\Http\Controllers;

use App\Models\Content\Portfolio;
use App\Models\Core\User;
use Illuminate\Contracts\View\View;

class PortfolioShowcaseController extends Controller
{
    public function __invoke(string $slug): View
    {
        $entry = Portfolio::query()
            ->publiclyVisible()
            ->where('slug', $slug)
            ->whereHas('user', fn ($query) => $query
                ->where('is_active', true)
                ->whereNull('deactivated_at')
                ->where('profile_visibility', 'public')
                ->whereHas('roles', fn ($roles) => $roles->where('name', User::ROLE_STUDENT)))
            ->firstOrFail(['id', 'user_id']);

        $owner = $entry->user()->firstOrFail(['id', 'name', 'bio', 'occupation']);
        $projects = Portfolio::query()
            ->publiclyVisible()
            ->where('user_id', $owner->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'id', 'title', 'slug', 'description', 'technologies', 'project_url',
                'source_code_url', 'category', 'status', 'sort_order',
            ]);

        return view('portfolio.showcase', [
            'owner' => [
                'name' => $owner->name,
                'bio' => $owner->bio,
                'occupation' => $owner->occupation,
            ],
            'projects' => $projects,
        ]);
    }
}
