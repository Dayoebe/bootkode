<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DashboardSidebar extends Component
{
    public $activeLink = 'dashboard';
    public $user;

    public function mount()
    {
        $this->user = Auth::user();

        if (!app()->runningInConsole()) {
            $currentRouteName = request()->route()?->getName() ?? 'dashboard';
            $routeMap = config('menu.route_map', []);
            $this->activeLink = $routeMap[$currentRouteName] ?? 'dashboard';
        }
    }

    // A computed property to get the filtered menu items
    public function getFilteredMenuItemsProperty()
    {
        $user = $this->user;
        $menuItems = config('menu.items', []);

        // Super Admin sees everything
        if ($user && $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            return $menuItems;
        }

        $filteredItems = [];
        foreach ($menuItems as $item) {
            $includeItem = false;

            // Check if the user has any of the roles for the main item.
            // An empty 'roles' array means it's for all users.
            if (!isset($item['roles']) || empty($item['roles'])) {
                $includeItem = true;
            } elseif ($user && $user->hasAnyRole($item['roles'])) {
                $includeItem = true;
            }

            // If the main item is not included, we skip it completely.
            if (!$includeItem) {
                continue;
            }

            // Now, handle the children of the item.
            if (isset($item['children']) && !empty($item['children'])) {
                $item['children'] = $this->filterMenuChildren($item['children'], $user);
                // If a parent item has no children after filtering, we can remove it
                // unless it has a valid route itself.
                if (empty($item['children']) && $item['route_name'] === '#') {
                    continue;
                }
            }
            
            // Add the item to our list of filtered items
            $filteredItems[] = $item;
        }

        return $filteredItems;
    }

    /**
     * Recursively filters menu children based on user roles.
     *
     * @param array $children
     * @param User $user
     * @return array
     */
    private function filterMenuChildren(array $children, $user): array
    {
        $filteredChildren = [];
        foreach ($children as $child) {
            $includeChild = false;

            // An empty 'roles' array means it's for all users.
            if (!isset($child['roles']) || empty($child['roles'])) {
                $includeChild = true;
            } elseif ($user && $user->hasAnyRole($child['roles'])) {
                $includeChild = true;
            }

            if ($includeChild) {
                // Check for nested children
                if (isset($child['children']) && !empty($child['children'])) {
                    $child['children'] = $this->filterMenuChildren($child['children'], $user);
                }
                $filteredChildren[] = $child;
            }
        }
        return $filteredChildren;
    }

    private function generateMobileMenuItems($menuItems)
    {
        $maxMobileItems = 5;
        $mobileItems = [];
        $count = 0;

        foreach ($menuItems as $item) {
            if ($count >= $maxMobileItems - 1) {
                break;
            }

            $mobileItem = [
                'label' => $item['label'],
                'icon' => $item['icon'],
                'route_name' => $item['route_name'],
                'link_id' => $item['link_id'] ?? Str::slug($item['label']),
                'badge' => $item['badge'] ?? false,
            ];

            if (isset($item['children']) && !empty($item['children'])) {
                $mobileItem['children'] = array_map(function ($child) {
                    return array_merge($child, ['link_id' => $child['link_id'] ?? Str::slug($child['label'])]);
                }, $item['children']);
            }

            $mobileItems[] = $mobileItem;
            $count++;
        }

        if (count($menuItems) > $maxMobileItems) {
            $remainingItems = array_slice($menuItems, $maxMobileItems - 1);
            if (!empty($remainingItems)) {
                $mobileItems[$maxMobileItems - 1] = [
                    'label' => 'More',
                    'icon' => 'fas fa-ellipsis-h',
                    'route_name' => '#',
                    'link_id' => 'more',
                    'badge' => false,
                    'children' => array_map(function ($item) {
                        return array_merge($item, ['link_id' => $item['link_id'] ?? Str::slug($item['label'])]);
                    }, $remainingItems),
                ];
            }
        }

        return $mobileItems;
    }

    public function render()
    {
        $menuItems = $this->filteredMenuItems;
        $mobileMenuItems = $this->generateMobileMenuItems($menuItems);

        return view('livewire.dashboard-sidebar', [
            'menuItems' => $menuItems,
            'mobileMenuItems' => $mobileMenuItems,
            'user' => $this->user,
            'activeLink' => $this->activeLink,
        ]);
    }
}
