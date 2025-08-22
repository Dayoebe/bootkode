<?php

use App\Models\User;
return [
    'items' => [

        [
            'label' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'route_name' => 'dashboard',
            'roles' => [],
            'link_id' => 'dashboard',
            'children' => [
                ['label' => 'Super Admin Dashboard', 'icon' => 'fas fa-user-shield', 'route_name' => 'super_admin.dashboard', 'roles' => [User::ROLE_SUPER_ADMIN], 'link_id' => 'super_admin_dashboard',],
                ['label' => 'Academy Admin Dashboard', 'icon' => 'fas fa-school', 'route_name' => 'academy_admin.dashboard', 'roles' => [User::ROLE_ACADEMY_ADMIN], 'link_id' => 'academy_admin_dashboard',],
                ['label' => 'Instructor Dashboard', 'icon' => 'fas fa-chalkboard-teacher', 'route_name' => 'instructor.dashboard', 'roles' => [User::ROLE_INSTRUCTOR], 'link_id' => 'instructor_dashboard',],
                ['label' => 'Mentor Dashboard', 'icon' => 'fas fa-user-graduate', 'route_name' => 'mentor.dashboard', 'roles' => [User::ROLE_MENTOR], 'link_id' => 'mentor_dashboard',],
                ['label' => 'Content Editor Dashboard', 'icon' => 'fas fa-edit', 'route_name' => 'content_editor.dashboard', 'roles' => [User::ROLE_CONTENT_EDITOR], 'link_id' => 'content_editor_dashboard',],
                ['label' => 'Affiliate Ambassador Dashboard', 'icon' => 'fas fa-handshake', 'route_name' => 'affiliate_ambassador.dashboard', 'roles' => [User::ROLE_AFFILIATE_AMBASSADOR], 'link_id' => 'affiliate_ambassador_dashboard',],
                ['label' => 'Student Dashboard', 'icon' => 'fas fa-book-reader', 'route_name' => 'student.dashboard', 'roles' => [User::ROLE_STUDENT], 'link_id' => 'student_dashboard',],
            ]
        ],
        [
            'label' => 'Profile',
            'icon' => 'fas fa-user',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'profile',
            'children' => [
                ['label' => 'View Profile', 'icon' => 'fas fa-user', 'route_name' => 'profile.view', 'roles' => [], 'link_id' => 'profile.view',],
                ['label' => 'Edit Profile', 'icon' => 'fas fa-user-edit', 'route_name' => 'profile.edit', 'roles' => [], 'link_id' => 'profile.edit',],
            ]
        ],
        [
            'label' => 'User Management',
            'icon' => 'fas fa-users-cog',
            'route_name' => ('user-management'),
            'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN],
            'link_id' => 'user_management',
            'children' => [
                ['label' => 'User Activity', 'icon' => 'fas fa-history', 'route_name' => 'user.activity', 'roles' => [User::ROLE_SUPER_ADMIN], 'link_id' => 'user.activity',],
                ['label' => 'Edit User', 'icon' => 'fas fa-user-edit', 'route_name' => 'user-management', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'user.edit',],
                ['label' => 'All Users', 'icon' => 'fas fa-user-friends', 'route_name' => 'all-users', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'all-users',],
                ['label' => 'Roles & Permissions', 'icon' => 'fas fa-user-tag', 'route_name' => 'roles-permissions', 'roles' => [User::ROLE_SUPER_ADMIN],'link_id' => 'roles-permissions',],
                ['label' => 'Pending Verifications', 'icon' => 'fas fa-user-clock', 'route_name' => 'pending-verifications', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN], 'link_id' => 'pending-verifications',],
            ]
        ],

        [
            'label' => 'Course Management',
            'icon' => 'fas fa-book',
            'route_name' => '#',
            'roles' => [],
            'link_id' => '',
            'children' => [
                ['label' => 'All Courses', 'icon' => 'fas fa-list', 'route_name' => ('all-course'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_INSTRUCTOR, User::ROLE_MENTOR, User::ROLE_CONTENT_EDITOR], 'link_id' => 'all-courses',],
                ['label' => 'Available Courses', 'icon' => 'fas fa-book-open', 'route_name' => ('courses.available'), 'roles' => [User::ROLE_STUDENT], 'link_id' => 'courses.available',],
                ['label' => 'Create Course', 'icon' => 'fas fa-plus-circle', 'route_name' => ('create_course'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_INSTRUCTOR], 'link_id' => 'create-course',],
                ['label' => 'Edit Course', 'icon' => 'fas fa-edit', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_INSTRUCTOR], 'link_id' => 'edit-course',],
                ['label' => 'Course Builder', 'icon' => 'fas fa-puzzle-piece', 'route_name' => ('all-course'), 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'all-course'],
                ['label' => 'Course Categories', 'icon' => 'fas fa-tags', 'route_name' => ('course-categories'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR], 'link_id' => 'course-categories',],
                ['label' => 'Course Reviews', 'icon' => 'fas fa-star', 'route_name' => ('course-reviews'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_INSTRUCTOR], 'link_id' => 'course-reviews',],
                ['label' => 'Course Approvals', 'icon' => 'fas fa-check-circle', 'route_name' => ('course-approvals'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'course-approvals',],
            ]
        ],
        [
            'label' => 'Learning Hub',
            'icon' => 'fas fa-graduation-cap',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'learning_hub',
            'children' => [
                ['label' => 'My Courses', 'icon' => 'fas fa-book', 'route_name' => ('student.enrolled-courses'), 'roles' => [User::ROLE_STUDENT], 'component' => 'enrolled-courses', 'link_id' => 'student.enrolled-courses',],
                ['label' => 'Course Catalog', 'icon' => 'fas fa-book-open', 'route_name' => ('student.course-catalog'), 'roles' => [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],],
                ['label' => 'Learning Analytics', 'icon' => 'fas fa-chart-line', 'route_name' => 'student.learning-analytics', 'roles' => [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN], 'component' => 'learning-analytics'],
                ['label' => 'Saved Resources', 'icon' => 'fas fa-bookmark', 'route_name' => ('student.saved-resources'), 'roles' => [User::ROLE_STUDENT], 'link_id' => 'student.saved-resources'],
                ['label' => 'Offline Learning', 'icon' => 'fas fa-download', 'route_name' => ('student.offline-learning'), 'roles' => [User::ROLE_STUDENT], 'link_id' => 'student.offline-learning',]
            ]
        ],
        [
            'label' => 'Assessment Center',
            'icon' => 'fas fa-clipboard-check',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'assessment_center',
            'children' => [
                ['label' => 'My Quizzes', 'icon' => 'fas fa-list-alt', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT]],
                ['label' => 'Create Quiz', 'icon' => 'fas fa-plus-square', 'route_name' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Quiz Analytics', 'icon' => 'fas fa-chart-bar', 'route_name' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Quiz Library', 'icon' => 'fas fa-book', 'route_name' => '#', 'roles' => [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Assessment Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
            ],
        ],
        [
            'label' => 'Community Center',
            'icon' => 'fas fa-users',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'community',
            'children' => [
                ['label' => 'Discussion Forums', 'icon' => 'fas fa-comments', 'route_name' => '#', 'roles' => []],
                ['label' => 'Study Groups', 'icon' => 'fas fa-user-friends', 'route_name' => '#', 'roles' => []],
                ['label' => 'Code Challenges', 'icon' => 'fas fa-trophy', 'route_name' => '#', 'roles' => []],
                ['label' => 'Live Events', 'icon' => 'fas fa-video', 'route_name' => '#', 'roles' => []],
                ['label' => 'Community Moderation', 'icon' => 'fas fa-shield-alt', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Feedback System', 'icon' => 'fas fa-comments', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR]],
            ]
        ],
        [
            'label' => 'Certification',
            'icon' => 'fas fa-certificate',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'certification',
            'children' => [
                ['label' => 'My Certificates', 'icon' => 'fas fa-award', 'route_name' => ('certificates.index'), 'roles' => [], 'link_id' => 'certificates.index',],
                ['label' => 'Request Certificates', 'icon' => 'fas fa-file-alt', 'route_name' => ('certificates.request'), 'roles' => [], 'link_id' => 'certificates.request',],
                ['label' => 'Certificate Templates', 'icon' => 'fas fa-file-invoice', 'route_name' => ('certificates.templates'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR], 'link_id' => 'certificates.templates',],
                ['label' => 'Certificate Approvals', 'icon' => 'fas fa-check-double', 'route_name' => ('certificates.approvals'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'certificates.approvals',],
                ['label' => 'Bulk Issue Certificates', 'icon' => 'fas fa-file-alt', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'certificates.bulk',],
                [
                    'label' => 'Verify Certificates',
                    'icon' => 'fas fa-check-circle',
                    'route_name' => 'certificates.public-verify',  // Changed from '/certificates/verify' to the route name
                    'permissions' => [],
                    'link_id' => 'certificates.public-verify'
                ]
            ]
        ],
        [
            'label' => 'Mentorship Network',
            'icon' => 'fas fa-hands-helping',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'mentorship',
            'children' => [
                ['label' => 'Find a Mentor', 'icon' => 'fas fa-search', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT]],
                ['label' => 'Mentor Dashboard', 'icon' => 'fas fa-chalkboard-teacher', 'route_name' => '#', 'roles' => [User::ROLE_MENTOR]],
                ['label' => 'Mentorship Requests', 'icon' => 'fas fa-bell', 'route_name' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Code Review System', 'icon' => 'fas fa-code', 'route_name' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_STUDENT]],
                ['label' => 'Session Scheduling', 'icon' => 'fas fa-calendar-check', 'route_name' => '#', 'roles' => [User::ROLE_MENTOR, User::ROLE_STUDENT]],
                ['label' => 'Mentor Resources', 'icon' => 'fas fa-tools', 'route_name' => '#', 'roles' => [User::ROLE_MENTOR]],
                ['label' => 'Mentor Management', 'icon' => 'fas fa-user-tie', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'AI Learning Tools',
            'icon' => 'fas fa-robot',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'ai_tools',
            'children' => [
                ['label' => 'Code Assistant', 'icon' => 'fas fa-code', 'route_name' => '#', 'roles' => []],
                ['label' => 'Interview Prep Bot', 'icon' => 'fas fa-comment-dots', 'route_name' => '#', 'roles' => []],
                ['label' => 'Learning Recommendations', 'icon' => 'fas fa-lightbulb', 'route_name' => '#', 'roles' => []],
                ['label' => 'AI Tool Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Career Services',
            'icon' => 'fas fa-briefcase',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'career_services',
            'children' => [
                ['label' => 'Job Board', 'icon' => 'fas fa-search-dollar', 'route_name' => '#', 'roles' => []],
                ['label' => 'Portfolio Builder', 'icon' => 'fas fa-id-card', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT]],
                ['label' => 'Resume Generator', 'icon' => 'fas fa-file-alt', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT]],
                ['label' => 'Mock Interviews', 'icon' => 'fas fa-comments', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT]],
                ['label' => 'Employer Connections', 'icon' => 'fas fa-handshake', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Job Portal',
            'icon' => 'fas fa-briefcase',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'job_portal',
            'children' => [
                ['label' => 'Job Listings', 'icon' => 'fas fa-list', 'route_name' => '#', 'roles' => []],
                ['label' => 'Post a Job', 'icon' => 'fas fa-plus-square', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Job Applications', 'icon' => 'fas fa-file-alt', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Resume Database', 'icon' => 'fas fa-database', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Employer Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Job Alerts', 'icon' => 'fas fa-bell', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT]],
                ['label' => 'Job Categories', 'icon' => 'fas fa-tags', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Job Analytics', 'icon' => 'fas fa-chart-bar', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Job Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Content Management',
            'icon' => 'fas fa-edit',
            'route_name' => '#',
            'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'content_management',
            'children' => [
                ['label' => 'Learning Materials', 'icon' => 'fas fa-book', 'route_name' => '#', 'roles' => []],
                ['label' => 'Video Library', 'icon' => 'fas fa-video', 'route_name' => '#', 'roles' => []],
                ['label' => 'Documentation', 'icon' => 'fas fa-file-alt', 'route_name' => '#', 'roles' => []],
                ['label' => 'Localization', 'icon' => 'fas fa-language', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Content Moderation', 'icon' => 'fas fa-shield-alt', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
            ]
        ],
        [
            'label' => 'Doc. Management',
            'icon' => 'fas fa-book',
            'route_name' => '#',
            'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'documentation_management',
            'children' => [
                ['label' => 'All Documents', 'icon' => 'fas fa-file-alt', 'route_name' => '#', 'roles' => []],
                ['label' => 'Create Document', 'icon' => 'fas fa-plus-circle', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Document Categories', 'icon' => 'fas fa-tags', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Document Reviews', 'icon' => 'fas fa-star', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Document Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Blog Management',
            'icon' => 'fas fa-blog',
            'route_name' => '#',
            'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'blog_management',
            'children' => [
                ['label' => 'All Posts', 'icon' => 'fas fa-newspaper', 'route_name' => '#', 'roles' => []],
                ['label' => 'Create Post', 'icon' => 'fas fa-plus-circle', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Categories', 'icon' => 'fas fa-tags', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Manage Blog Posts', 'icon' => 'fas fa-newspaper', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Comments Moderation', 'icon' => 'fas fa-comments', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'SEO Settings', 'icon' => 'fas fa-search', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_SUPER_ADMIN]],
            ],
        ],
        [
            'label' => 'Library Management',
            'icon' => 'fas fa-book',
            'route_name' => '#',
            'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'library_management',
            'children' => [
                ['label' => 'All Books', 'icon' => 'fas fa-book-open', 'route_name' => '#', 'roles' => []],
                ['label' => 'Add New Book', 'icon' => 'fas fa-plus', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Manage Categories', 'icon' => 'fas fa-tags', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Library Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ['label' => 'Library Resources', 'icon' => 'fas fa-book-open', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Library Analytics', 'icon' => 'fas fa-chart-line', 'route_name' => '#', 'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Library Management', 'icon' => 'fas fa-book', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Add New Library Item', 'icon' => 'fas fa-plus', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Library Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Institution Portal',
            'icon' => 'fas fa-university',
            'route_name' => '#',
            'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'institution',
            'children' => [
                ['label' => 'Partner Schools', 'icon' => 'fas fa-school', 'route_name' => '#', 'roles' => []],
                ['label' => 'License Management', 'icon' => 'fas fa-key', 'route_name' => '#', 'roles' => []],
                ['label' => 'Bulk Enrollment', 'icon' => 'fas fa-user-plus', 'route_name' => '#', 'roles' => []],
                ['label' => 'Institution Analytics', 'icon' => 'fas fa-chart-pie', 'route_name' => '#', 'roles' => []],
                ['label' => 'White-label Settings', 'icon' => 'fas fa-paint-roller', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Financial Center',
            'icon' => 'fas fa-money-bill-wave',
            'route_name' => '#',
            'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'financial',
            'children' => [
                ['label' => 'Revenue Dashboard', 'icon' => 'fas fa-chart-line', 'route_name' => '#', 'roles' => []],
                ['label' => 'Payment Processing', 'icon' => 'fas fa-credit-card', 'route_name' => '#', 'roles' => []],
                ['label' => 'Subscription Plans', 'icon' => 'fas fa-receipt', 'route_name' => '#', 'roles' => []],
                ['label' => 'Scholarship Program', 'icon' => 'fas fa-graduation-cap', 'route_name' => '#', 'roles' => []],
                ['label' => 'Expense Tracking', 'icon' => 'fas fa-file-invoice-dollar', 'route_name' => '#', 'roles' => []],
                ['label' => 'Tax Configuration', 'icon' => 'fas fa-percentage', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Gamification',
            'icon' => 'fas fa-gamepad',
            'route_name' => '#',
            'roles' => [User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'gamification',
            'children' => [
                ['label' => 'Badge System', 'icon' => 'fas fa-medal', 'route_name' => '#', 'roles' => []],
                ['label' => 'Leaderboards', 'icon' => 'fas fa-trophy', 'route_name' => '#', 'roles' => []],
                ['label' => 'Rewards Store', 'icon' => 'fas fa-gift', 'route_name' => '#', 'roles' => []],
                ['label' => 'Achievement Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => []],
            ]
        ],
        [
            'label' => 'Affiliate Network',
            'icon' => 'fas fa-share-alt',
            'route_name' => '#',
            'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN],
            'link_id' => 'affiliate',
            'children' => [
                ['label' => 'Referral Dashboard', 'icon' => 'fas fa-chart-bar', 'route_name' => '#', 'roles' => []],
                ['label' => 'Marketing Tools', 'icon' => 'fas fa-bullhorn', 'route_name' => '#', 'roles' => []],
                ['label' => 'Commission History', 'icon' => 'fas fa-money-bill-wave', 'route_name' => '#', 'roles' => [User::ROLE_AFFILIATE_AMBASSADOR, User::ROLE_SUPER_ADMIN]],
                ['label' => 'Commission Reports', 'icon' => 'fas fa-coins', 'route_name' => '#', 'roles' => []],
                ['label' => 'Performance Analytics', 'icon' => 'fas fa-chart-pie', 'route_name' => '#', 'roles' => []],
                ['label' => 'Affiliate Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],

        [
            'label' => 'Support & Feedback',
            'icon' => 'fas fa-headset',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'support_feedback',
            'children' => [
                ['label' => 'Help & Support', 'icon' => 'fas fa-question-circle', 'route_name' => ('help.support'), 'roles' => [], 'link_id' => 'help.support',],
                ['label' => 'Support Tickets', 'icon' => 'fas fa-ticket-alt', 'route_name' => ('support.tickets'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'support.tickets',],
                ['label' => 'FAQ Management', 'icon' => 'fas fa-book-open', 'route_name' => ('faq.management'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'faq.management',],
                ['label' => 'Feedback', 'icon' => 'fas fa-comment-dots', 'route_name' => ('feedback'), 'roles' => [], 'link_id' => 'feedback',],
                ['label' => 'Feedback Management', 'icon' => 'fas fa-comments', 'route_name' => ('feedback.management'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'feedback.management',],
                ['label' => 'Announcements', 'icon' => 'fas fa-bullhorn', 'route_name' => ('announcements'), 'roles' => [], 'link_id' => 'announcements',],
                ['label' => 'Announcement Management', 'icon' => 'fas fa-bullhorn', 'route_name' => ('announcement.management'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'announcement.management',],
            ]
        ],
        [
            'label' => 'System Status',
            'icon' => 'fas fa-server',
            'route_name' => ('system-status'),
            'roles' => [],
            'link_id' => 'system-status',
            'children' => [
                ['label' => 'System Status', 'icon' => 'fas fa-tools', 'route_name' => ('system-status'), 'roles' => [], 'link_id' => 'system-status',],
                ['label' => 'System Status Management', 'icon' => 'fas fa-tools', 'route_name' => ('system-status.management'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'system-status.management',],
            ]
        ],
        [
            'label' => 'Settings',
            'icon' => 'fas fa-cog',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'settings',
            'children' => [
                ['label' => 'Account Management', 'icon' => 'fas fa-user-shield', 'route_name' => ('settings'), 'roles' => []],
                ['label' => 'Profile Settings', 'icon' => 'fas fa-user-cog', 'route_name' => '#', 'roles' => []],
                ['label' => 'Notification Preferences', 'icon' => 'fas fa-bell', 'route_name' => '#', 'roles' => []],
                ['label' => 'Privacy Settings', 'icon' => 'fas fa-lock', 'route_name' => '#', 'roles' => []],
                ['label' => 'Language & Localization', 'icon' => 'fas fa-language', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'CBT',
            'icon' => 'fas fa-laptop-code',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'cbt',
            'children' => [
                ['label' => 'Take CBT Exam', 'icon' => 'fas fa-pencil-alt', 'route_name' => '#', 'roles' => [User::ROLE_STUDENT], 'link_id' => 'cbt.exam', 'has_submenu' => true, 'exams' => []],
                ['label' => 'View CBT Results', 'icon' => 'fas fa-chart-bar', 'route_name' => ('cbt.results'), 'roles' => [User::ROLE_STUDENT], 'link_id' => 'cbt.results',],
                ['label' => 'CBT Management', 'icon' => 'fas fa-cog', 'route_name' => ('cbt.management'), 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_INSTRUCTOR], 'link_id' => 'cbt.management',],
            ]
        ],
        [
            'label' => 'Notifications',
            'icon' => 'fas fa-bell',
            'route_name' => 'notifications',
            'roles' => [],
            'link_id' => 'notifications',
        ],
        [
            'label' => 'System Administration',
            'icon' => 'fas fa-cogs',
            'route_name' => '#',
            'roles' => [User::ROLE_SUPER_ADMIN],
            'link_id' => 'system_admin',
            'children' => [
                ['label' => 'Platform Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ['label' => 'API Management', 'icon' => 'fas fa-code-branch', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ['label' => 'Database Management', 'icon' => 'fas fa-database', 'route_name' => '#', 'roles' => []],
                ['label' => 'Integrations', 'icon' => 'fas fa-puzzle-piece', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ['label' => 'System Logs', 'icon' => 'fas fa-clipboard-list', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ['label' => 'Server Monitoring', 'icon' => 'fas fa-heartbeat', 'route_name' => '#', 'roles' => []],
                ['label' => 'Announcements', 'icon' => 'fas fa-bullhorn', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ['label' => 'Backup & Restore', 'icon' => 'fas fa-save', 'route_name' => '#', 'roles' => []],
                ['label' => 'Security Center', 'icon' => 'fas fa-shield-alt', 'route_name' => '#', 'roles' => []],
            ]
        ],
        [
            'label' => 'Financial',
            'icon' => 'fas fa-wallet',
            'route_name' => '#',
            'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN],
            'link_id' => 'Financial',
            'children' => [
                ['label' => 'Revenue Reports', 'icon' => 'fas fa-chart-line', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Payouts', 'icon' => 'fas fa-money-check-alt', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
                ['label' => 'Subscriptions', 'icon' => 'fas fa-credit-card', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN]],
            ]
        ],
        [
            'label' => 'Newsletter',
            'icon' => 'fas fa-envelope',
            'route_name' => '#',
            'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN],
            'link_id' => 'newsletter',
            'children' => [
                ['label' => 'Manage Subscribers', 'icon' => 'fas fa-users', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Create Campaign', 'icon' => 'fas fa-plus-circle', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Campaign Analytics', 'icon' => 'fas fa-chart-bar', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN]],
                ['label' => 'Newsletter Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],
        [
            'label' => 'Page Management',
            'icon' => 'fas fa-bars',
            'route_name' => '#',
            'roles' => [User::ROLE_SUPER_ADMIN],
            'link_id' => 'page_management',
            'children' => [
                ['label' => 'Page Custom Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'page-custom-settings',],
                ['label' => 'Page Custom Blocks', 'icon' => 'fas fa-columns', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'page-custom-blocks',],
                ['label' => 'Page Custom Shortcodes', 'icon' => 'fas fa-code', 'route_name' => '#', 'roles' => [User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN], 'link_id' => 'page-custom-shortcodes',],
            ]
        ],
        [
            'label' => 'Search',
            'icon' => 'fas fa-search',
            'route_name' => '#',
            'roles' => [],
            'link_id' => 'search',
            'children' => [
                ['label' => 'Global Search', 'icon' => 'fas fa-search', 'route_name' => '#', 'roles' => []],
                ['label' => 'Advanced Search', 'icon' => 'fas fa-filter', 'route_name' => '#', 'roles' => []],
                ['label' => 'Search Settings', 'icon' => 'fas fa-cog', 'route_name' => '#', 'roles' => [User::ROLE_SUPER_ADMIN]],
            ]
        ],


    ]
];