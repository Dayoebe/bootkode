<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
            // Map common routes to their menu link IDs for better active state detection
            $routeMap = [
                'dashboard' => 'dashboard',
                'profile.view' => 'profile.view',
                'profile.edit' => 'profile.view',
                'my-course' => 'my-courses',
                'all-course' => 'all-courses',
                'student.enrolled-courses' => 'student.enrolled-courses',
                'courses.available' => 'courses.available',
                'create_course' => 'create-course',
                'cbt.management' => 'cbt.management',
                'cbt.exam' => 'cbt.exam',
                'cbt.viewer' => 'cbt.viewer',
                'admin.certificates.manage' => 'admin.certificates.manage',
                'certificates.index' => 'certificates.index',
                'marketplace.browse' => 'marketplace.browse',
                'community.center' => 'community',
                'settings' => 'settings',
                'notifications' => 'notifications',
            ];
            
            $this->activeLink = $routeMap[$currentRouteName] ?? $this->determineActiveLinkFromRoute($currentRouteName);
        }
    }

    /**
     * Determine active link from route name patterns
     */
    private function determineActiveLinkFromRoute(string $routeName): string
    {
        // Handle common patterns
        if (str_starts_with($routeName, 'student.')) {
            return str_replace(['student.', '.'], ['', '_'], $routeName);
        }
        
        if (str_starts_with($routeName, 'admin.certificates.')) {
            return 'admin.certificates.manage';
        }
        
        if (str_starts_with($routeName, 'marketplace.')) {
            return 'marketplace';
        }
        
        if (str_starts_with($routeName, 'community.')) {
            return 'community';
        }
        
        if (str_starts_with($routeName, 'blog.') || str_starts_with($routeName, 'admin.blog.')) {
            return 'blog_management';
        }
        
        if (str_starts_with($routeName, 'affiliate.')) {
            return 'affiliate';
        }

        // Default fallback
        return str_replace(['.', '-'], '_', $routeName);
    }

    /**
     * Get filtered menu items based on user roles
     */
    public function getFilteredMenuItemsProperty()
    {
        $user = $this->user;
        $menuItems = config('menu.items', []);

        // Super Admin sees everything
        if ($user && $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            return $this->processMenuItems($menuItems);
        }

        $filteredItems = [];
        foreach ($menuItems as $item) {
            $includeItem = $this->shouldIncludeMenuItem($item, $user);

            if (!$includeItem) {
                continue;
            }

            // Handle children
            if (isset($item['children']) && !empty($item['children'])) {
                $item['children'] = $this->filterMenuChildren($item['children'], $user);
                // Remove parent if it has no accessible children and no direct route
                if (empty($item['children']) && $item['route_name'] === '#') {
                    continue;
                }
            }

            $filteredItems[] = $item;
        }

        return $this->processMenuItems($filteredItems);
    }

    /**
     * Process menu items to add any computed properties
     */
    private function processMenuItems(array $items): array
    {
        return array_map(function ($item) {
            // Add notification counts or other dynamic data here if needed
            if (isset($item['children'])) {
                $item['children'] = $this->processMenuItems($item['children']);
            }
            return $item;
        }, $items);
    }

    /**
     * Check if a menu item should be included based on user roles
     */
    private function shouldIncludeMenuItem(array $item, $user): bool
    {
        // Empty roles array means accessible to all
        if (!isset($item['roles']) || empty($item['roles'])) {
            return true;
        }

        // Check if user has required roles
        return $user && $user->hasAnyRole($item['roles']);
    }

    /**
     * Recursively filter menu children based on user roles
     */
    private function filterMenuChildren(array $children, $user): array
    {
        $filteredChildren = [];
        
        foreach ($children as $child) {
            if ($this->shouldIncludeMenuItem($child, $user)) {
                // Handle nested children if they exist
                if (isset($child['children']) && !empty($child['children'])) {
                    $child['children'] = $this->filterMenuChildren($child['children'], $user);
                }
                $filteredChildren[] = $child;
            }
        }
        
        return $filteredChildren;
    }

    /**
     * Generate mobile menu items (simplified version of desktop menu)
     */
    private function generateMobileMenuItems($menuItems): array
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
                    return array_merge($child, [
                        'link_id' => $child['link_id'] ?? Str::slug($child['label'])
                    ]);
                }, $item['children']);
            }

            $mobileItems[] = $mobileItem;
            $count++;
        }

        // Add "More" menu if there are remaining items
        if (count($menuItems) > $maxMobileItems - 1) {
            $remainingItems = array_slice($menuItems, $maxMobileItems - 1);
            if (!empty($remainingItems)) {
                $mobileItems[] = [
                    'label' => 'More',
                    'icon' => 'fas fa-ellipsis-h',
                    'route_name' => '#',
                    'link_id' => 'more',
                    'badge' => false,
                    'children' => array_map(function ($item) {
                        return array_merge($item, [
                            'link_id' => $item['link_id'] ?? Str::slug($item['label'])
                        ]);
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