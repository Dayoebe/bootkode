<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class DashboardSidebar extends Component
{
    // Public property to track the active link/section for highlighting
    public $activeLink = 'dashboard';

    /**
     * Mount the component.
     * You might initialize activeLink based on the current URL here if needed.
     */
    public function mount()
    {
        // Example: Set active link based on current route name
        $currentRouteName = request()->route()->getName();
        if (str_contains($currentRouteName, 'super_admin')) {
            $this->activeLink = 'super_admin_dashboard';
        } elseif (str_contains($currentRouteName, 'academy_admin')) {
            $this->activeLink = 'academy_admin_dashboard';
        } elseif (str_contains($currentRouteName, 'instructor')) {
            $this->activeLink = 'instructor_dashboard';
        } elseif (str_contains($currentRouteName, 'mentor')) {
            $this->activeLink = 'mentor_dashboard';
        } elseif (str_contains($currentRouteName, 'content_editor')) {
            $this->activeLink = 'content_editor_dashboard';
        } elseif (str_contains($currentRouteName, 'affiliate_ambassador')) {
            $this->activeLink = 'affiliate_ambassador_dashboard';
        } elseif (str_contains($currentRouteName, 'student')) {
            $this->activeLink = 'student_dashboard';
        } else {
            $this->activeLink = 'dashboard'; // Default if no specific role route matches
        }
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        $user = auth()->user(); // Get the authenticated user

        // Define the full menu structure
        $menuItems = [
            // General Dashboard (Visible to all authenticated)
            [
                'label' => 'Dashboard Overview',
                'icon' => 'fas fa-tachometer-alt',
                'route' => '#',
                'roles' => [],
                'link_id' => 'dashboard'
            ],

            // User Management (Super Admin, Academy Admin)
            [
                'label' => 'User Management',
                'icon' => 'fas fa-users',
                'route' => '#',
                'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN],
                'link_id' => 'user_management',
                'children' => [
                    ['label' => 'All Users', 'icon' => 'fas fa-user-friends', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN]],
                    ['label' => 'Roles & Permissions', 'icon' => 'fas fa-user-tag', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Pending Verifications', 'icon' => 'fas fa-user-clock', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ]
            ],

            // Courses (All authenticated, with specific actions for instructors/admins)
            [
                'label' => 'Courses',
                'icon' => 'fas fa-book',
                'route' => '#',
                'roles' => [],
                'link_id' => 'courses',
                'children' => [
                    ['label' => 'Browse All Courses', 'icon' => 'fas fa-list', 'route' => '#', 'roles' => []],
                    ['label' => 'My Enrolled Courses', 'icon' => 'fas fa-book-reader', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                    ['label' => 'Create New Course', 'icon' => 'fas fa-plus-circle', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Manage My Courses', 'icon' => 'fas fa-chalkboard-teacher', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR]],
                    ['label' => 'Course Approvals', 'icon' => 'fas fa-check-double', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ]
            ],

            // Mentorship (All authenticated, with specific actions for mentors/admins)
            [
                'label' => 'Mentorship',
                'icon' => 'fas fa-hands-helping',
                'route' => '#',
                'roles' => [],
                'link_id' => 'mentorship',
                'children' => [
                    ['label' => 'Find a Mentor', 'icon' => 'fas fa-search', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                    ['label' => 'My Mentees', 'icon' => 'fas fa-users', 'route' => '#', 'roles' => [User::ROLE_MENTOR]],
                    ['label' => 'Mentorship Requests', 'icon' => 'fas fa-bell', 'route' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Schedule Sessions', 'icon' => 'fas fa-calendar-alt', 'route' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_STUDENT]],
                    ['label' => 'Code Review Submissions', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => [User::ROLE_MENTOR]],
                ]
            ],

            // Certifications (All authenticated, with specific actions for admins)
            [
                'label' => 'Certifications',
                'icon' => 'fas fa-certificate',
                'route' => '#',
                'roles' => [],
                'link_id' => 'certifications',
                'children' => [
                    ['label' => 'My Certificates', 'icon' => 'fas fa-award', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                    ['label' => 'Request Certificate', 'icon' => 'fas fa-file-signature', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                    ['label' => 'Approve Certificates', 'icon' => 'fas fa-check-circle', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ]
            ],

            // Community (All authenticated)
            [
                'label' => 'Community Forum',
                'icon' => 'fas fa-comments',
                'route' => '#',
                'roles' => [],
                'link_id' => 'community'
            ],

            // Content Management (Content Editor, Academy Admin, Super Admin)
            [
                'label' => 'Content Management',
                'icon' => 'fas fa-edit',
                'route' => '#',
                'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'content_management',
                'children' => [
                    ['label' => 'Manage Blog Posts', 'icon' => 'fas fa-newspaper', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Manage Documentation', 'icon' => 'fas fa-file-alt', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Media Library', 'icon' => 'fas fa-photo-video', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ['label' => 'SEO Settings', 'icon' => 'fas fa-globe', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                ]
            ],

            // Affiliate Program (Affiliate/Ambassador, Super Admin)
            [
                'label' => 'Affiliate Program',
                'icon' => 'fas fa-share-alt',
                'route' => '#',
                'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN],
                'link_id' => 'affiliate_program',
                'children' => [
                    ['label' => 'Referral Dashboard', 'icon' => 'fas fa-chart-bar', 'route' => '#', 'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Commission History', 'icon' => 'fas fa-money-bill-wave', 'route' => '#', 'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Marketing Assets', 'icon' => 'fas fa-bullhorn', 'route' => '#', 'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN]],
                ]
            ],

            // System Administration (Super Admin)
            [
                'label' => 'System Administration',
                'icon' => 'fas fa-cogs',
                'route' => '#',
                'roles' => [User::ROLE_SUPER_ADMIN],
                'link_id' => 'system_admin',
                'children' => [
                    ['label' => 'Platform Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ['label' => 'API Management', 'icon' => 'fas fa-code-branch', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Integrations', 'icon' => 'fas fa-puzzle-piece', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ['label' => 'System Logs', 'icon' => 'fas fa-clipboard-list', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Announcements', 'icon' => 'fas fa-bullhorn', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ]
            ],

            // Financials (Super Admin, Academy Admin)
            [
                'label' => 'Financials',
                'icon' => 'fas fa-wallet',
                'route' => '#',
                'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN],
                'link_id' => 'financials',
                'children' => [
                    ['label' => 'Revenue Reports', 'icon' => 'fas fa-chart-line', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN]],
                    ['label' => 'Payouts', 'icon' => 'fas fa-money-check-alt', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ['label' => 'Subscriptions', 'icon' => 'fas fa-credit-card', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN]],
                ]
            ],

            // Student Specific (Student)
            [
                'label' => 'My Progress',
                'icon' => 'fas fa-chart-pie',
                'route' => '#',
                'roles' => [User::ROLE_STUDENT],
                'link_id' => 'my_progress'
            ],
            [
                'label' => 'Quizzes & Assessments',
                'icon' => 'fas fa-clipboard-check',
                'route' => '#',
                'roles' => [User::ROLE_STUDENT],
                'link_id' => 'quizzes'
            ],
            [
                'label' => 'Support Tickets',
                'icon' => 'fas fa-headset',
                'route' => '#',
                'roles' => [User::ROLE_STUDENT],
                'link_id' => 'support_tickets'
            ],
            [
                'label' => 'My Profile',
                'icon' => 'fas fa-user-circle',
                'route' => '#',
                'roles' => [], // All authenticated users have a profile
                'link_id' => 'my_profile'
            ],
        ];

        // Filter menu items based on user role
        $filteredMenuItems = [];
        foreach ($menuItems as $item) {
            // If Super Admin, show all items
            if ($user && $user->isSuperAdmin()) {
                $filteredMenuItems[] = $item;
            }
            // If roles array is empty, it's visible to all authenticated users
            elseif ($user && empty($item['roles'])) {
                $filteredMenuItems[] = $item;
            }
            // If user has one of the specified roles
            elseif ($user && !empty($item['roles']) && $user->hasRoleIn($item['roles'])) {
                // If it's a parent item with children, filter its children too
                if (isset($item['children'])) {
                    $filteredChildren = [];
                    foreach ($item['children'] as $child) {
                        if (empty($child['roles']) || ($user && $user->isSuperAdmin()) || ($user && $user->hasRoleIn($child['roles']))) {
                            $filteredChildren[] = $child;
                        }
                    }
                    // Only add parent if it has visible children or is directly visible
                    if (!empty($filteredChildren) || empty($item['roles'])) {
                        $item['children'] = $filteredChildren;
                        $filteredMenuItems[] = $item;
                    }
                } else {
                    $filteredMenuItems[] = $item;
                }
            }
        }

        return view('livewire.dashboard-sidebar', [
            'menuItems' => $filteredMenuItems,
            'user' => $user // Pass the user object to the view for displaying name/role
        ]);
    }
}