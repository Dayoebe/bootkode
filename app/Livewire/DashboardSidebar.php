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
        } elseif (str_contains($currentRouteName, 'profile')) { // Add this block for profile routes
            $this->activeLink = 'profile_management';
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
            // Dashboard Overview
            // add view, edit profile and other relevant children
            [
                'label' => 'Dashboard Overview',
                'icon' => 'fas fa-tachometer-alt',
                'route' => '#',
                'roles' => [],
                'link_id' => 'dashboard',
                'children' => [
                        ['label' => 'View Profile', 'icon' => 'fas fa-user', 'route' => route('profile.view'), 'roles' => [], 'link_id' => 'dashboard'],
                        ['label' => 'Edit Profile', 'icon' => 'fas fa-user-edit', 'route' => route('profile.edit'), 'roles' => [], 'link_id' => 'edit_profile'],
                        ['label' => 'Notifications', 'icon' => 'fas fa-bell', 'route' => '#', 'roles' => []],
                        ['label' => 'Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => []],
                        ['label' => 'Help & Support', 'icon' => 'fas fa-question-circle', 'route' => '#', 'roles' => []],
                        ['label' => 'Feedback', 'icon' => 'fas fa-comment-dots', 'route' => '#', 'roles' => []],
                        ['label' => 'Announcements', 'icon' => 'fas fa-bullhorn', 'route' => '#', 'roles' => []],
                        ['label' => 'System Status', 'icon' => 'fas fa-server', 'route' => '#', 'roles' => []],
                    ],
            ],

            // Course Management
            [
                'label' => 'Course Management',
                'icon' => 'fas fa-laptop-code',
                'route' => '#',
                'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'course_management',
                'children' => [
                        ['label' => 'All Courses', 'icon' => 'fas fa-list', 'route' => '#', 'roles' => []],
                        ['label' => 'Create Course', 'icon' => 'fas fa-plus-circle', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Course Builder', 'icon' => 'fas fa-puzzle-piece', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Course Categories', 'icon' => 'fas fa-tags', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Course Reviews', 'icon' => 'fas fa-star', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Course Approvals', 'icon' => 'fas fa-check-double', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Learning Management
            [
                'label' => 'Learning Hub',
                'icon' => 'fas fa-graduation-cap',
                'route' => '#',
                'roles' => [],
                'link_id' => 'learning_hub',
                'children' => [
                        ['label' => 'My Learning Path', 'icon' => 'fas fa-road', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Course Catalog', 'icon' => 'fas fa-book-open', 'route' => '#', 'roles' => []],
                        ['label' => 'Learning Analytics', 'icon' => 'fas fa-chart-line', 'route' => '#', 'roles' => [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Saved Resources', 'icon' => 'fas fa-bookmark', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Offline Learning', 'icon' => 'fas fa-download', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                    ]
            ],


            // Certification System
            [
                'label' => 'Certification Center',
                'icon' => 'fas fa-certificate',
                'route' => '#',
                'roles' => [],
                'link_id' => 'certification',
                'children' => [
                        ['label' => 'My Certificates', 'icon' => 'fas fa-award', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Request Certificate', 'icon' => 'fas fa-file-signature', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Certificate Templates', 'icon' => 'fas fa-stamp', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Verify Certificates', 'icon' => 'fas fa-check-circle', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Bulk Certificate Issuance', 'icon' => 'fas fa-barcode', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Mentorship Network
            [
                'label' => 'Mentorship Network',
                'icon' => 'fas fa-hands-helping',
                'route' => '#',
                'roles' => [],
                'link_id' => 'mentorship',
                'children' => [
                        ['label' => 'Find a Mentor', 'icon' => 'fas fa-search', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Mentor Dashboard', 'icon' => 'fas fa-chalkboard-teacher', 'route' => '#', 'roles' => [User::ROLE_MENTOR]],
                        ['label' => 'Mentorship Requests', 'icon' => 'fas fa-bell', 'route' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Code Review System', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_STUDENT]],
                        ['label' => 'Session Scheduling', 'icon' => 'fas fa-calendar-check', 'route' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_STUDENT]],
                        ['label' => 'Mentor Resources', 'icon' => 'fas fa-tools', 'route' => '#', 'roles' => [User::ROLE_MENTOR]],
                        ['label' => 'Mentor Management', 'icon' => 'fas fa-user-tie', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Assessment & Quizzes
            [
                'label' => 'Assessment Center',
                'icon' => 'fas fa-clipboard-check',
                'route' => '#',
                'roles' => [],
                'link_id' => 'assessment_center',
                'children' => [
                        ['label' => 'My Quizzes', 'icon' => 'fas fa-list-alt', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Create Quiz', 'icon' => 'fas fa-plus-square', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Quiz Analytics', 'icon' => 'fas fa-chart-bar', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Quiz Library', 'icon' => 'fas fa-book', 'route' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Assessment Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ],
            ],


            // Community
            [
                'label' => 'Community Center',
                'icon' => 'fas fa-users',
                'route' => '#',
                'roles' => [],
                'link_id' => 'community',
                'children' => [
                        ['label' => 'Discussion Forums', 'icon' => 'fas fa-comments', 'route' => '#', 'roles' => []],
                        ['label' => 'Study Groups', 'icon' => 'fas fa-user-friends', 'route' => '#', 'roles' => []],
                        ['label' => 'Code Challenges', 'icon' => 'fas fa-trophy', 'route' => '#', 'roles' => []],
                        ['label' => 'Live Events', 'icon' => 'fas fa-video', 'route' => '#', 'roles' => []],
                        ['label' => 'Community Moderation', 'icon' => 'fas fa-shield-alt', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Feedback System', 'icon' => 'fas fa-comments', 'route' => '#', 'roles' => [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR]],
                    ]
            ],

            // AI Learning Tools
            [
                'label' => 'AI Learning Tools',
                'icon' => 'fas fa-robot',
                'route' => '#',
                'roles' => [],
                'link_id' => 'ai_tools',
                'children' => [
                        ['label' => 'Code Assistant', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => []],
                        ['label' => 'Interview Prep Bot', 'icon' => 'fas fa-comment-dots', 'route' => '#', 'roles' => []],
                        ['label' => 'Learning Recommendations', 'icon' => 'fas fa-lightbulb', 'route' => '#', 'roles' => []],
                        ['label' => 'AI Tool Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Career Services
            [
                'label' => 'Career Services',
                'icon' => 'fas fa-briefcase',
                'route' => '#',
                'roles' => [],
                'link_id' => 'career_services',
                'children' => [
                        ['label' => 'Job Board', 'icon' => 'fas fa-search-dollar', 'route' => '#', 'roles' => []],
                        ['label' => 'Portfolio Builder', 'icon' => 'fas fa-id-card', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Resume Generator', 'icon' => 'fas fa-file-alt', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Mock Interviews', 'icon' => 'fas fa-comments', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Employer Connections', 'icon' => 'fas fa-handshake', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Content Management
            [
                'label' => 'Content Management',
                'icon' => 'fas fa-edit',
                'route' => '#',
                'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'content_management',
                'children' => [
                        ['label' => 'Learning Materials', 'icon' => 'fas fa-book', 'route' => '#', 'roles' => []],
                        ['label' => 'Video Library', 'icon' => 'fas fa-video', 'route' => '#', 'roles' => []],
                        ['label' => 'Documentation', 'icon' => 'fas fa-file-alt', 'route' => '#', 'roles' => []],
                        ['label' => 'Localization', 'icon' => 'fas fa-language', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Content Moderation', 'icon' => 'fas fa-shield-alt', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                    ]
            ],

            // Documentation Management
            [
                'label' => 'Doc. Management',
                'icon' => 'fas fa-book',
                'route' => '#',
                'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'documentation_management',
                'children' => [
                        ['label' => 'All Documents', 'icon' => 'fas fa-file-alt', 'route' => '#', 'roles' => []],
                        ['label' => 'Create Document', 'icon' => 'fas fa-plus-circle', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Document Categories', 'icon' => 'fas fa-tags', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Document Reviews', 'icon' => 'fas fa-star', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Document Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ]
            ],
            // blog
            [
                'label' => 'Blog Management',
                'icon' => 'fas fa-blog',
                'route' => '#',
                'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'blog_management',
                'children' => [
                        ['label' => 'All Posts', 'icon' => 'fas fa-newspaper', 'route' => '#', 'roles' => []],
                        ['label' => 'Create Post', 'icon' => 'fas fa-plus-circle', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Categories', 'icon' => 'fas fa-tags', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Manage Blog Posts', 'icon' => 'fas fa-newspaper', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Comments Moderation', 'icon' => 'fas fa-comments', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'SEO Settings', 'icon' => 'fas fa-search', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                    ],
            ],
            // Library Management
            [
                'label' => 'Library Management',
                'icon' => 'fas fa-book',
                'route' => '#',
                'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'library_management',
                'children' => [
                        ['label' => 'All Books', 'icon' => 'fas fa-book-open', 'route' => '#', 'roles' => []],
                        ['label' => 'Add New Book', 'icon' => 'fas fa-plus', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Manage Categories', 'icon' => 'fas fa-tags', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Library Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Library Resources', 'icon' => 'fas fa-book-open', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Library Analytics', 'icon' => 'fas fa-chart-line', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Library Management', 'icon' => 'fas fa-book', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Add New Library Item', 'icon' => 'fas fa-plus', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Library Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ]
            ],
            // Job Portal
            [
                'label' => 'Job Portal',
                'icon' => 'fas fa-briefcase',
                'route' => '#',
                'roles' => [],
                'link_id' => 'job_portal',
                'children' => [
                        ['label' => 'Job Listings', 'icon' => 'fas fa-list', 'route' => '#', 'roles' => []],
                        ['label' => 'Post a Job', 'icon' => 'fas fa-plus-square', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Job Applications', 'icon' => 'fas fa-file-alt', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Resume Database', 'icon' => 'fas fa-database', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Employer Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Job Alerts', 'icon' => 'fas fa-bell', 'route' => '#', 'roles' => [User::ROLE_STUDENT]],
                        ['label' => 'Job Categories', 'icon' => 'fas fa-tags', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Job Analytics', 'icon' => 'fas fa-chart-bar', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Job Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ]
            ],


            // User Management
            [
                'label' => 'User Management',
                'icon' => 'fas fa-users-cog',
                'route' => '#',
                'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'user_management',
                'children' => [
                        ['label' => 'All Users', 'icon' => 'fas fa-users', 'route' => '#', 'roles' => []],
                        ['label' => 'User Groups', 'icon' => 'fas fa-object-group', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Bulk Import', 'icon' => 'fas fa-file-import', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Access Control', 'icon' => 'fas fa-user-shield', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Activity Logs', 'icon' => 'fas fa-clipboard-list', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Institution Management
            [
                'label' => 'Institution Portal',
                'icon' => 'fas fa-university',
                'route' => '#',
                'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'institution',
                'children' => [
                        ['label' => 'Partner Schools', 'icon' => 'fas fa-school', 'route' => '#', 'roles' => []],
                        ['label' => 'License Management', 'icon' => 'fas fa-key', 'route' => '#', 'roles' => []],
                        ['label' => 'Bulk Enrollment', 'icon' => 'fas fa-user-plus', 'route' => '#', 'roles' => []],
                        ['label' => 'Institution Analytics', 'icon' => 'fas fa-chart-pie', 'route' => '#', 'roles' => []],
                        ['label' => 'White-label Settings', 'icon' => 'fas fa-paint-roller', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Financial System
            [
                'label' => 'Financial Center',
                'icon' => 'fas fa-money-bill-wave',
                'route' => '#',
                'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'financials',
                'children' => [
                        ['label' => 'Revenue Dashboard', 'icon' => 'fas fa-chart-line', 'route' => '#', 'roles' => []],
                        ['label' => 'Payment Processing', 'icon' => 'fas fa-credit-card', 'route' => '#', 'roles' => []],
                        ['label' => 'Subscription Plans', 'icon' => 'fas fa-receipt', 'route' => '#', 'roles' => []],
                        ['label' => 'Scholarship Program', 'icon' => 'fas fa-graduation-cap', 'route' => '#', 'roles' => []],
                        ['label' => 'Expense Tracking', 'icon' => 'fas fa-file-invoice-dollar', 'route' => '#', 'roles' => []],
                        ['label' => 'Tax Configuration', 'icon' => 'fas fa-percentage', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            // Gamification System
            [
                'label' => 'Gamification',
                'icon' => 'fas fa-gamepad',
                'route' => '#',
                'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'gamification',
                'children' => [
                        ['label' => 'Badge System', 'icon' => 'fas fa-medal', 'route' => '#', 'roles' => []],
                        ['label' => 'Leaderboards', 'icon' => 'fas fa-trophy', 'route' => '#', 'roles' => []],
                        ['label' => 'Rewards Store', 'icon' => 'fas fa-gift', 'route' => '#', 'roles' => []],
                        ['label' => 'Achievement Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => []],
                    ]
            ],

            // System Administration
            [
                'label' => 'System Administration',
                'icon' => 'fas fa-server',
                'route' => '#',
                'roles' => [User::ROLE_SUPER_ADMIN],
                'link_id' => 'system_admin',
                'children' => [
                        ['label' => 'Platform Settings', 'icon' => 'fas fa-cogs', 'route' => '#', 'roles' => []],
                        ['label' => 'API Management', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => []],
                        ['label' => 'Database Management', 'icon' => 'fas fa-database', 'route' => '#', 'roles' => []],
                        ['label' => 'Server Monitoring', 'icon' => 'fas fa-heartbeat', 'route' => '#', 'roles' => []],
                        ['label' => 'Backup & Restore', 'icon' => 'fas fa-save', 'route' => '#', 'roles' => []],
                        ['label' => 'Security Center', 'icon' => 'fas fa-shield-alt', 'route' => '#', 'roles' => []],
                    ]
            ],

            // Personal Tools
            [
                'label' => 'My Tools',
                'icon' => 'fas fa-toolbox',
                'route' => '#',
                'roles' => [],
                'link_id' => 'my_tools',
                'children' => [
                        ['label' => 'Profile Settings', 'icon' => 'fas fa-user-cog', 'route' => '#', 'roles' => []],
                        ['label' => 'Notification Center', 'icon' => 'fas fa-bell', 'route' => '#', 'roles' => []],
                        ['label' => 'Privacy Controls', 'icon' => 'fas fa-lock', 'route' => '#', 'roles' => []],
                        ['label' => 'Download History', 'icon' => 'fas fa-download', 'route' => '#', 'roles' => []],
                        ['label' => 'Support Center', 'icon' => 'fas fa-question-circle', 'route' => '#', 'roles' => []],
                    ]
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


            // Affiliate Program
            [
                'label' => 'Affiliate Network',
                'icon' => 'fas fa-share-alt',
                'route' => '#',
                'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN],
                'link_id' => 'affiliate',
                'children' => [
                        ['label' => 'Referral Dashboard', 'icon' => 'fas fa-chart-bar', 'route' => '#', 'roles' => []],
                        ['label' => 'Marketing Tools', 'icon' => 'fas fa-bullhorn', 'route' => '#', 'roles' => []],
                        ['label' => 'Commission History', 'icon' => 'fas fa-money-bill-wave', 'route' => '#', 'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Commission Reports', 'icon' => 'fas fa-coins', 'route' => '#', 'roles' => []],
                        ['label' => 'Performance Analytics', 'icon' => 'fas fa-chart-pie', 'route' => '#', 'roles' => []],
                        ['label' => 'Affiliate Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
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

            // newsletter 
            [
                'label' => 'Newsletter',
                'icon' => 'fas fa-envelope',
                'route' => '#',
                'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'newsletter',
                'children' => [
                        ['label' => 'Manage Subscribers', 'icon' => 'fas fa-users', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Create Campaign', 'icon' => 'fas fa-plus-circle', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Campaign Analytics', 'icon' => 'fas fa-chart-bar', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Newsletter Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ]
            ],

            //contact us
            [
                'label' => 'Contact Us',
                'icon' => 'fas fa-envelope',
                'route' => '#',
                'roles' => [],
                'link_id' => 'contact_us',
                'children' => [
                        ['label' => 'Support', 'icon' => 'fas fa-headset', 'route' => '#', 'roles' => []],
                        ['label' => 'Feedback', 'icon' => 'fas fa-comment-dots', 'route' => '#', 'roles' => []],
                        ['label' => 'Report Issue', 'icon' => 'fas fa-exclamation-triangle', 'route' => '#', 'roles' => []],
                    ]
            ],

            //search functionality
            [
                'label' => 'Search',
                'icon' => 'fas fa-search',
                'route' => '#',
                'roles' => [],
                'link_id' => 'search',
                'children' => [
                        ['label' => 'Global Search', 'icon' => 'fas fa-search', 'route' => '#', 'roles' => []],
                        ['label' => 'Advanced Search', 'icon' => 'fas fa-filter', 'route' => '#', 'roles' => []],
                        ['label' => 'Search Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                    ]
            ],
            [
                'label' => 'Web Pages',
                'icon' => 'fas fa-file-alt',
                'route' => '#',
                'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
                'link_id' => 'web_pages',
                'children' => [
                        ['label' => 'All Pages', 'icon' => 'fas fa-list', 'route' => '#', 'roles' => []],
                        ['label' => 'Create Page', 'icon' => 'fas fa-plus-circle', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Templates', 'icon' => 'fas fa-file', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Builder', 'icon' => 'fas fa-puzzle-piece', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page SEO', 'icon' => 'fas fa-search', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Analytics', 'icon' => 'fas fa-chart-line', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Page Categories', 'icon' => 'fas fa-tags', 'route' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                        ['label' => 'Page Reviews', 'icon' => 'fas fa-star', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Footer', 'icon' => 'fas fa-ellipsis-h', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Header', 'icon' => 'fas fa-ellipsis-v', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Navigation', 'icon' => 'fas fa-bars', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Sidebar', 'icon' => 'fas fa-bars', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Widgets', 'icon' => 'fas fa-th-large', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Media Library', 'icon' => 'fas fa-images', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Content Blocks', 'icon' => 'fas fa-columns', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom CSS', 'icon' => 'fas fa-paint-brush', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom JS', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Fonts', 'icon' => 'fas fa-font', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Colors', 'icon' => 'fas fa-palette', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Layouts', 'icon' => 'fas fa-th', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Scripts', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Styles', 'icon' => 'fas fa-paint-brush', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom HTML', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Widgets', 'icon' => 'fas fa-th-large', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Shortcodes', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Templates', 'icon' => 'fas fa-file-alt', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Blocks', 'icon' => 'fas fa-columns', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Elements', 'icon' => 'fas fa-cubes', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Features', 'icon' => 'fas fa-star', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Settings', 'icon' => 'fas fa-cog', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Blocks', 'icon' => 'fas fa-columns', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                        ['label' => 'Page Custom Shortcodes', 'icon' => 'fas fa-code', 'route' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                    ]
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