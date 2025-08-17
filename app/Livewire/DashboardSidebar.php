<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DashboardSidebar extends Component
{
    public $activeLink = 'dashboard';
    public $user;

    public function mount()
    {
        // Cache the authenticated user
        $this->user = Auth::user();

        // Only attempt to get the route name in an HTTP context
        if (!app()->runningInConsole()) {
            $currentRouteName = request()->route()?->getName() ?? 'dashboard';
            $routeMap = config('menu.route_map', [
                'super_admin.dashboard' => 'super_admin_dashboard',
                'academy_admin.dashboard' => 'academy_admin_dashboard',
                'instructor.dashboard' => 'instructor_dashboard',
                'mentor.dashboard' => 'mentor_dashboard',
                'content_editor.dashboard' => 'content_editor_dashboard',
                'affiliate_ambassador.dashboard' => 'affiliate_ambassador_dashboard',
                'student.dashboard' => 'student_dashboard',
                'profile.edit' => 'profile_management',
                'course_management' => 'all-course',
                'course-categories' => 'course_management.course_categories',
                'course-builder' => 'course_management.course_builder',
                'course-reviews' => 'course_management.course_reviews',
                'course-approvals' => 'course_management.course_approvals',
                'user-management' => 'user-management',
                'user.activity' => 'user.activity',
                'settings' => 'settings',
                'notifications' => 'notifications',
                'help.support' => 'help.support',
                'support.tickets' => 'support.tickets',
                'faq.management' => 'faq.management',
                'feedback' => 'feedback',
                'feedback.management' => 'feedback.management',
                'announcements' => 'announcements',
                'announcement.management' => 'announcement.management',
                'system-status' => 'system-status',
                'system-status.management' => 'system-status.management',
                'courses.available' => 'courses.available',
                'certificates.index' => 'certificates.index',
                'certificates.request' => 'certificates.request',
                'certificates.templates' => 'certificates.templates',
                'certificates.approvals' => 'certificates.approvals',
                'certificates.public-verify' => 'certificates.public-verify',
                'certificates.bulk' => 'certificates.bulk',
                'certificates.approval' => 'certificates.approval',
                'certificates.download' => 'certificates.download',
                'course.preview' => 'course.preview',
                'edit-course' => 'edit-course',
                'courses.enroll' => 'courses.enroll',
                'courses.start' => 'courses.start',
                'courses.show' => 'courses.show',
                'courses.approvals' => 'courses.approvals',
                'courses.reviews' => 'courses.reviews',
                'courses.create' => 'courses.create',
                'courses.categories' => 'courses.categories',
                'courses.all' => 'courses.all',
            ]);

            $this->activeLink = $routeMap[$currentRouteName] ?? 'dashboard';
        }
    }

    /**
     * Get filtered menu items based on user roles, cached for performance.
     *
     * @return array
     */
    public function getFilteredMenuItemsProperty()
    {
        $user = $this->user;

        return Cache::remember('filtered_menu_' . ($user ? $user->id : 'guest'), 3600, function () use ($user) {
            $menuItems = config('menu.items', []);
            $filteredMenuItems = [];

            foreach ($menuItems as $item) {
                if ($user && $user->hasRole('super_admin')) {
                    $filteredMenuItems[] = $item;
                } elseif (empty($item['roles'])) {
                    $filteredMenuItems[] = $item;
                } elseif ($user && !empty($item['roles']) && $user->hasAnyRole($item['roles'])) {
                    if (isset($item['children'])) {
                        $filteredChildren = $this->filterMenuChildren($item['children'], $user);
                        if (!empty($filteredChildren) || empty($item['roles'])) {
                            $item['children'] = $filteredChildren;
                            $filteredMenuItems[] = $item;
                        }
                    } else {
                        $filteredMenuItems[] = $item;
                    }
                }
            }

            return $filteredMenuItems;
        });
    }

    /**
     * Filter menu children by user roles.
     *
     * @param array $children
     * @param \App\Models\User|null $user
     * @return array
     */
    private function filterMenuChildren($children, $user)
    {
        return array_filter($children, function ($child) use ($user) {
            return empty($child['roles']) || ($user && $user->hasRole('super_admin')) || ($user && $user->hasAnyRole($child['roles']));
        });
    }

    /**
     * Generate mobile menu items from desktop menu items.
     *
     * @param array $menuItems
     * @return array
     */
    private function generateMobileMenuItems($menuItems)
    {
        $mobileItems = [];
        $maxMobileItems = config('menu.max_mobile_items', 5);

        foreach (array_slice($menuItems, 0, $maxMobileItems) as $item) {
            if (!isset($item['link_id'])) {
                Log::warning("Missing link_id for menu item: {$item['label']}");
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
                    if (!isset($child['link_id'])) {
                        Log::warning("Missing link_id for child menu item: {$child['label']}");
                    }
                    return array_merge($child, ['link_id' => $child['link_id'] ?? Str::slug($child['label'])]);
                }, $item['children']);
            }

            $mobileItems[] = $mobileItem;
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
                        if (!isset($item['link_id'])) {
                            Log::warning("Missing link_id for more menu item: {$item['label']}");
                        }
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