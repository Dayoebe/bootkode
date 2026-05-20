<?php

namespace App\Services;

use App\Models\Core\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class DashboardSearchService
{
    public function search(?string $term, ?User $user = null, int $limit = 6): array
    {
        $term = trim(Str::limit((string) $term, 100, ''));

        if ($term === '') {
            return [
                'q' => '',
                'total' => 0,
                'groups' => [],
                'results' => [],
            ];
        }

        $limit = max(1, min($limit, 15));
        $like = '%' . addcslashes($term, '\\%_') . '%';
        $groups = [];

        foreach ($this->providers() as $provider) {
            try {
                $group = $provider($term, $like, $user, $limit);

                if (!empty($group['items'])) {
                    $groups[] = $group;
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        $results = collect($groups)
            ->flatMap(fn (array $group) => collect($group['items'])
                ->map(fn (array $item) => $item + [
                    'group' => $group['label'],
                    'type' => $group['type'],
                ]))
            ->values()
            ->all();

        return [
            'q' => $term,
            'total' => count($results),
            'groups' => $groups,
            'results' => $results,
        ];
    }

    private function providers(): array
    {
        return [
            fn ($term, $like, $user, $limit) => $this->courses($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->lessons($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->sections($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->assessments($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->users($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->certificates($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->pages($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->blogPosts($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->documents($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->learningMaterials($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->videos($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->marketplaceItems($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->supportTickets($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->announcements($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->faqs($term, $like, $user, $limit),
            fn ($term, $like, $user, $limit) => $this->community($term, $like, $user, $limit),
        ];
    }

    private function courses(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('courses')) {
            return $this->group('courses', 'Courses', 'fas fa-book-open', []);
        }

        $query = DB::table('courses')
            ->leftJoin('users as instructors', 'instructors.id', '=', 'courses.instructor_id')
            ->select([
                'courses.id',
                'courses.title',
                'courses.subtitle',
                'courses.slug',
                'courses.description',
                'courses.difficulty_level',
                'courses.is_published',
                'courses.is_approved',
                'instructors.name as instructor_name',
            ]);

        $this->whereLike($query, [
            'courses.title',
            'courses.subtitle',
            'courses.description',
            'courses.difficulty_level',
            'courses.tags',
            'instructors.name',
        ], $like);

        if (!$this->canManageCourses($user)) {
            $query->where('courses.is_published', true)->where('courses.is_approved', true);
        }

        $items = $query
            ->orderByDesc('courses.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($course) => [
                'title' => $course->title,
                'description' => $this->summary($course->subtitle ?: $course->description),
                'meta' => trim('Course' . ($course->difficulty_level ? ' · ' . ucfirst($course->difficulty_level) : '') . ($course->instructor_name ? ' · ' . $course->instructor_name : '')),
                'url' => $this->routeUrl('courses.preview', ['course' => $course->id], '/course-management/all-courses'),
                'icon' => 'fas fa-book-open',
            ])
            ->all();

        return $this->group('courses', 'Courses', 'fas fa-book-open', $items);
    }

    private function lessons(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('lessons') || !Schema::hasTable('sections') || !Schema::hasTable('courses')) {
            return $this->group('lessons', 'Lessons', 'fas fa-play-circle', []);
        }

        $query = DB::table('lessons')
            ->join('sections', 'sections.id', '=', 'lessons.section_id')
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->select([
                'lessons.id',
                'lessons.title',
                'lessons.description',
                'lessons.content_type',
                'lessons.duration_minutes',
                'courses.id as course_id',
                'courses.title as course_title',
                'courses.slug as course_slug',
                'sections.title as section_title',
            ]);

        $this->whereLike($query, [
            'lessons.title',
            'lessons.description',
            'lessons.content',
            'lessons.text_content',
            'lessons.content_type',
            'sections.title',
            'courses.title',
        ], $like);

        $this->visibleCourseContent($query, $user);

        $items = $query
            ->orderByDesc('lessons.updated_at')
            ->limit($limit)
            ->get()
            ->map(function ($lesson) use ($user) {
                $url = $this->canManageCourses($user)
                    ? $this->routeUrl('course-builder', ['course' => $lesson->course_id, 'lesson' => $lesson->id], '/course-management/all-courses')
                    : $this->routeUrl('course.view', ['course' => $lesson->course_slug, 'lesson' => $lesson->id], '/enrolled-courses');

                return [
                    'title' => $lesson->title,
                    'description' => $this->summary($lesson->description),
                    'meta' => trim('Lesson · ' . $lesson->course_title . ($lesson->section_title ? ' · ' . $lesson->section_title : '')),
                    'url' => $url,
                    'icon' => 'fas fa-play-circle',
                ];
            })
            ->all();

        return $this->group('lessons', 'Lessons', 'fas fa-play-circle', $items);
    }

    private function sections(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('sections') || !Schema::hasTable('courses')) {
            return $this->group('sections', 'Modules', 'fas fa-layer-group', []);
        }

        $query = DB::table('sections')
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->select([
                'sections.id',
                'sections.title',
                'sections.description',
                'sections.type',
                'courses.id as course_id',
                'courses.title as course_title',
                'courses.slug as course_slug',
            ]);

        $this->whereLike($query, [
            'sections.title',
            'sections.description',
            'sections.type',
            'courses.title',
        ], $like);

        $this->visibleCourseContent($query, $user, false);

        $items = $query
            ->orderByDesc('sections.updated_at')
            ->limit($limit)
            ->get()
            ->map(function ($section) use ($user) {
                $url = $this->canManageCourses($user)
                    ? $this->routeUrl('course-builder', ['course' => $section->course_id], '/course-management/all-courses')
                    : $this->routeUrl('course.view', ['course' => $section->course_slug], '/enrolled-courses');

                return [
                    'title' => $section->title,
                    'description' => $this->summary($section->description),
                    'meta' => ucfirst($section->type ?: 'Module') . ' · ' . $section->course_title,
                    'url' => $url,
                    'icon' => 'fas fa-layer-group',
                ];
            })
            ->all();

        return $this->group('sections', 'Modules', 'fas fa-layer-group', $items);
    }

    private function assessments(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('assessments')) {
            return $this->group('assessments', 'Assessments', 'fas fa-clipboard-check', []);
        }

        $query = DB::table('assessments')
            ->leftJoin('courses', 'courses.id', '=', 'assessments.course_id')
            ->leftJoin('lessons', 'lessons.id', '=', 'assessments.lesson_id')
            ->leftJoin('sections', 'sections.id', '=', 'assessments.section_id')
            ->select([
                'assessments.id',
                'assessments.title',
                'assessments.description',
                'assessments.type',
                'assessments.lesson_id',
                'courses.id as course_id',
                'courses.title as course_title',
                'courses.slug as course_slug',
                'lessons.title as lesson_title',
            ]);

        $this->whereLike($query, [
            'assessments.title',
            'assessments.description',
            'assessments.type',
            'assessments.instructions',
            'courses.title',
            'lessons.title',
            'sections.title',
        ], $like);

        $query->whereNull('assessments.deleted_at');

        if (!$this->canManageCourses($user)) {
            $query->whereNotNull('courses.id');
            $this->visibleCourseContent($query, $user, false);
        }

        $items = $query
            ->orderByDesc('assessments.updated_at')
            ->limit($limit)
            ->get()
            ->map(function ($assessment) use ($user) {
                if ($this->canManageCourses($user) && $assessment->course_id) {
                    $url = $this->routeUrl('course-builder', array_filter([
                        'course' => $assessment->course_id,
                        'lesson' => $assessment->lesson_id,
                    ]), '/course-management/all-courses');
                } elseif ($assessment->course_slug && $assessment->lesson_id) {
                    $url = $this->routeUrl('course.view', [
                        'course' => $assessment->course_slug,
                        'lesson' => $assessment->lesson_id,
                    ], '/enrolled-courses');
                } else {
                    $url = $this->routeUrl('cbt.exam.take', ['assessment' => $assessment->id], '/cbt/exams');
                }

                return [
                    'title' => $assessment->title,
                    'description' => $this->summary($assessment->description),
                    'meta' => trim(ucfirst($assessment->type ?: 'Assessment') . ($assessment->course_title ? ' · ' . $assessment->course_title : '') . ($assessment->lesson_title ? ' · ' . $assessment->lesson_title : '')),
                    'url' => $url,
                    'icon' => 'fas fa-clipboard-check',
                ];
            })
            ->all();

        return $this->group('assessments', 'Assessments', 'fas fa-clipboard-check', $items);
    }

    private function users(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('users')) {
            return $this->group('users', 'Users', 'fas fa-users', []);
        }

        $query = DB::table('users')
            ->select(['id', 'name', 'email', 'role', 'occupation', 'skills', 'is_active']);

        $this->whereLike($query, [
            'name',
            'email',
            'role',
            'phone_number',
            'occupation',
            'skills',
        ], $like);

        $items = $query
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn ($person) => [
                'title' => $person->name,
                'description' => $this->summary($person->occupation ?: $person->email),
                'meta' => 'User · ' . ucfirst(str_replace('_', ' ', $person->role ?: 'user')) . ($person->is_active ? '' : ' · Inactive'),
                'url' => $this->routeUrl('all-users', ['search' => $person->email ?: $person->name], '/dashboard/all-users'),
                'icon' => 'fas fa-user',
            ])
            ->all();

        return $this->group('users', 'Users', 'fas fa-users', $items);
    }

    private function certificates(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('certificates')) {
            return $this->group('certificates', 'Certificates', 'fas fa-certificate', []);
        }

        $query = DB::table('certificates')
            ->leftJoin('users', 'users.id', '=', 'certificates.user_id')
            ->leftJoin('courses', 'courses.id', '=', 'certificates.course_id')
            ->select([
                'certificates.id',
                'certificates.certificate_number',
                'certificates.title',
                'certificates.description',
                'certificates.status',
                'certificates.verification_token',
                'users.name as user_name',
                'users.email as user_email',
                'courses.title as course_title',
            ]);

        $this->whereLike($query, [
            'certificates.certificate_number',
            'certificates.title',
            'certificates.description',
            'certificates.status',
            'certificates.verification_token',
            'users.name',
            'users.email',
            'courses.title',
        ], $like);

        $query->whereNull('certificates.deleted_at');

        if (!$this->canManageCertificates($user) && $user) {
            $query->where('certificates.user_id', $user->id);
        }

        $items = $query
            ->orderByDesc('certificates.updated_at')
            ->limit($limit)
            ->get()
            ->map(function ($certificate) use ($user) {
                $url = $this->canManageCertificates($user)
                    ? $this->routeUrl('admin.certificates.manage', ['search' => $certificate->certificate_number], '/admin/certificates')
                    : $this->routeUrl('student.certificates.index', [], '/student/certificates');

                return [
                    'title' => $certificate->title ?: $certificate->certificate_number,
                    'description' => $this->summary($certificate->course_title ?: $certificate->description),
                    'meta' => trim('Certificate · ' . ucfirst($certificate->status ?: 'pending') . ($certificate->user_name ? ' · ' . $certificate->user_name : '')),
                    'url' => $url,
                    'icon' => 'fas fa-certificate',
                ];
            })
            ->all();

        return $this->group('certificates', 'Certificates', 'fas fa-certificate', $items);
    }

    private function pages(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('pages')) {
            return $this->group('pages', 'Pages', 'fas fa-file-lines', []);
        }

        $query = DB::table('pages')
            ->select(['id', 'title', 'slug', 'content', 'excerpt', 'status', 'meta_description', 'updated_at']);

        $this->whereLike($query, [
            'title',
            'slug',
            'content',
            'excerpt',
            'meta_description',
        ], $like);

        if (!$this->canManageContent($user)) {
            $query->where('status', 'published');
        }

        $items = $query
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($page) => [
                'title' => $page->title,
                'description' => $this->summary($page->excerpt ?: $page->meta_description ?: $page->content),
                'meta' => 'Page · ' . ucfirst($page->status ?: 'draft'),
                'url' => $page->status === 'published'
                    ? $this->routeUrl('page.show', ['slug' => $page->slug], '/' . $page->slug)
                    : $this->routeUrl('pages.index', ['search' => $page->title], '/admin/pages'),
                'icon' => 'fas fa-file-lines',
            ])
            ->all();

        return $this->group('pages', 'Pages', 'fas fa-file-lines', $items);
    }

    private function blogPosts(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('blog_posts')) {
            return $this->group('blog_posts', 'Blog Posts', 'fas fa-newspaper', []);
        }

        $query = DB::table('blog_posts')
            ->leftJoin('users as authors', 'authors.id', '=', 'blog_posts.author_id')
            ->select([
                'blog_posts.id',
                'blog_posts.title',
                'blog_posts.slug',
                'blog_posts.excerpt',
                'blog_posts.content',
                'blog_posts.status',
                'authors.name as author_name',
            ]);

        $this->whereLike($query, [
            'blog_posts.title',
            'blog_posts.excerpt',
            'blog_posts.content',
            'blog_posts.tags',
            'authors.name',
        ], $like);

        if (!$this->canManageContent($user)) {
            $query->where('blog_posts.status', 'published')->where('blog_posts.published_at', '<=', now());
        }

        $items = $query
            ->orderByDesc('blog_posts.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($post) => [
                'title' => $post->title,
                'description' => $this->summary($post->excerpt ?: $post->content),
                'meta' => trim('Blog · ' . ucfirst($post->status ?: 'draft') . ($post->author_name ? ' · ' . $post->author_name : '')),
                'url' => $post->status === 'published'
                    ? $this->routeUrl('blog.show', ['post' => $post->slug], '/blog/' . $post->slug)
                    : $this->routeUrl('admin.blog.posts.edit', ['post' => $post->slug], '/admin/blog/posts'),
                'icon' => 'fas fa-newspaper',
            ])
            ->all();

        return $this->group('blog_posts', 'Blog Posts', 'fas fa-newspaper', $items);
    }

    private function documents(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('documents')) {
            return $this->group('documents', 'Documents', 'fas fa-file-alt', []);
        }

        $query = DB::table('documents')
            ->leftJoin('document_categories', 'document_categories.id', '=', 'documents.category_id')
            ->select([
                'documents.id',
                'documents.title',
                'documents.content',
                'documents.excerpt',
                'documents.type',
                'documents.status',
                'document_categories.name as category_name',
            ]);

        $this->whereLike($query, [
            'documents.title',
            'documents.content',
            'documents.excerpt',
            'documents.tags',
            'documents.type',
            'document_categories.name',
        ], $like);

        $query->whereNull('documents.deleted_at');

        if (!$this->canManageContent($user)) {
            $query->where('documents.status', 'published')->where('documents.visibility', 'public');
        }

        $items = $query
            ->orderByDesc('documents.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($document) => [
                'title' => $document->title,
                'description' => $this->summary($document->excerpt ?: $document->content),
                'meta' => trim('Document · ' . ucfirst(str_replace('_', ' ', $document->type ?: 'article')) . ($document->category_name ? ' · ' . $document->category_name : '')),
                'url' => $this->routeUrl('content.all-documents', ['search' => $document->title], '/content/all-documents'),
                'icon' => 'fas fa-file-alt',
            ])
            ->all();

        return $this->group('documents', 'Documents', 'fas fa-file-alt', $items);
    }

    private function learningMaterials(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('learning_materials')) {
            return $this->group('learning_materials', 'Learning Materials', 'fas fa-folder-open', []);
        }

        $query = DB::table('learning_materials')
            ->leftJoin('courses', 'courses.id', '=', 'learning_materials.course_id')
            ->select([
                'learning_materials.id',
                'learning_materials.title',
                'learning_materials.description',
                'learning_materials.type',
                'learning_materials.status',
                'courses.title as course_title',
            ]);

        $this->whereLike($query, [
            'learning_materials.title',
            'learning_materials.description',
            'learning_materials.content',
            'learning_materials.tags',
            'learning_materials.type',
            'courses.title',
        ], $like);

        $query->whereNull('learning_materials.deleted_at');

        if (!$this->canManageContent($user)) {
            $query->where('learning_materials.status', 'published')->where('learning_materials.is_public', true);
        }

        $items = $query
            ->orderByDesc('learning_materials.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($material) => [
                'title' => $material->title,
                'description' => $this->summary($material->description),
                'meta' => trim('Material · ' . ucfirst($material->type ?: 'document') . ($material->course_title ? ' · ' . $material->course_title : '')),
                'url' => $this->routeUrl('content.learning-materials', ['search' => $material->title], '/content/learning-materials'),
                'icon' => 'fas fa-folder-open',
            ])
            ->all();

        return $this->group('learning_materials', 'Learning Materials', 'fas fa-folder-open', $items);
    }

    private function videos(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('video_libraries')) {
            return $this->group('videos', 'Videos', 'fas fa-video', []);
        }

        $query = DB::table('video_libraries')
            ->leftJoin('courses', 'courses.id', '=', 'video_libraries.course_id')
            ->leftJoin('lessons', 'lessons.id', '=', 'video_libraries.lesson_id')
            ->select([
                'video_libraries.id',
                'video_libraries.title',
                'video_libraries.description',
                'video_libraries.category',
                'video_libraries.status',
                'courses.title as course_title',
                'lessons.title as lesson_title',
            ]);

        $this->whereLike($query, [
            'video_libraries.title',
            'video_libraries.description',
            'video_libraries.tags',
            'video_libraries.category',
            'courses.title',
            'lessons.title',
        ], $like);

        $query->whereNull('video_libraries.deleted_at');

        if (!$this->canManageContent($user)) {
            $query->where('video_libraries.status', 'published')->where('video_libraries.is_public', true);
        }

        $items = $query
            ->orderByDesc('video_libraries.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($video) => [
                'title' => $video->title,
                'description' => $this->summary($video->description),
                'meta' => trim('Video · ' . ucfirst($video->category ?: 'tutorial') . ($video->course_title ? ' · ' . $video->course_title : '') . ($video->lesson_title ? ' · ' . $video->lesson_title : '')),
                'url' => $this->routeUrl('content.video-library', ['search' => $video->title], '/content/video-library'),
                'icon' => 'fas fa-video',
            ])
            ->all();

        return $this->group('videos', 'Videos', 'fas fa-video', $items);
    }

    private function marketplaceItems(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('marketplace_items')) {
            return $this->group('marketplace', 'Marketplace', 'fas fa-store', []);
        }

        $query = DB::table('marketplace_items')
            ->leftJoin('users as vendors', 'vendors.id', '=', 'marketplace_items.vendor_id')
            ->select([
                'marketplace_items.id',
                'marketplace_items.title',
                'marketplace_items.slug',
                'marketplace_items.description',
                'marketplace_items.short_description',
                'marketplace_items.type',
                'marketplace_items.status',
                'vendors.name as vendor_name',
            ]);

        $this->whereLike($query, [
            'marketplace_items.title',
            'marketplace_items.description',
            'marketplace_items.short_description',
            'marketplace_items.tags',
            'marketplace_items.keywords',
            'vendors.name',
        ], $like);

        $query->whereNull('marketplace_items.deleted_at');

        if (!$this->canManageMarketplace($user)) {
            $query->where('marketplace_items.status', 'approved');
        }

        $items = $query
            ->orderByDesc('marketplace_items.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'title' => $item->title,
                'description' => $this->summary($item->short_description ?: $item->description),
                'meta' => trim('Marketplace · ' . ucfirst($item->type ?: 'item') . ($item->vendor_name ? ' · ' . $item->vendor_name : '')),
                'url' => $this->routeUrl('marketplace.item.public', ['slug' => $item->slug], '/marketplace'),
                'icon' => 'fas fa-store',
            ])
            ->all();

        return $this->group('marketplace', 'Marketplace', 'fas fa-store', $items);
    }

    private function supportTickets(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('support_tickets')) {
            return $this->group('support', 'Support Tickets', 'fas fa-life-ring', []);
        }

        $query = DB::table('support_tickets')
            ->leftJoin('users', 'users.id', '=', 'support_tickets.user_id')
            ->select([
                'support_tickets.id',
                'support_tickets.subject',
                'support_tickets.description',
                'support_tickets.status',
                'users.name as user_name',
            ]);

        $this->whereLike($query, [
            'support_tickets.subject',
            'support_tickets.description',
            'support_tickets.response',
            'support_tickets.status',
            'users.name',
        ], $like);

        if (!$this->canManageSystem($user) && $user) {
            $query->where('support_tickets.user_id', $user->id);
        }

        $items = $query
            ->orderByDesc('support_tickets.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($ticket) => [
                'title' => $ticket->subject,
                'description' => $this->summary($ticket->description),
                'meta' => trim('Support · ' . ucfirst($ticket->status ?: 'open') . ($ticket->user_name ? ' · ' . $ticket->user_name : '')),
                'url' => $this->routeUrl('support.tickets', ['search' => $ticket->subject], '/dashboard/support-tickets'),
                'icon' => 'fas fa-life-ring',
            ])
            ->all();

        return $this->group('support', 'Support Tickets', 'fas fa-life-ring', $items);
    }

    private function announcements(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('announcements')) {
            return $this->group('announcements', 'Announcements', 'fas fa-bullhorn', []);
        }

        $query = DB::table('announcements')
            ->leftJoin('courses', 'courses.id', '=', 'announcements.course_id')
            ->select([
                'announcements.id',
                'announcements.title',
                'announcements.content',
                'announcements.status',
                'courses.title as course_title',
            ]);

        $this->whereLike($query, [
            'announcements.title',
            'announcements.content',
            'announcements.status',
            'courses.title',
        ], $like);

        if (!$this->canManageSystem($user)) {
            $query->where('announcements.status', 'published');
        }

        $items = $query
            ->orderByDesc('announcements.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($announcement) => [
                'title' => $announcement->title,
                'description' => $this->summary($announcement->content),
                'meta' => trim('Announcement · ' . ucfirst($announcement->status ?: 'draft') . ($announcement->course_title ? ' · ' . $announcement->course_title : '')),
                'url' => $this->routeUrl('announcements', ['search' => $announcement->title], '/dashboard/announcements'),
                'icon' => 'fas fa-bullhorn',
            ])
            ->all();

        return $this->group('announcements', 'Announcements', 'fas fa-bullhorn', $items);
    }

    private function faqs(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('faqs')) {
            return $this->group('faqs', 'FAQs', 'fas fa-circle-question', []);
        }

        $query = DB::table('faqs')
            ->select(['id', 'question', 'answer', 'is_published']);

        $this->whereLike($query, ['question', 'answer'], $like);

        if (!$this->canManageSystem($user)) {
            $query->where('is_published', true);
        }

        $items = $query
            ->orderBy('order')
            ->limit($limit)
            ->get()
            ->map(fn ($faq) => [
                'title' => $faq->question,
                'description' => $this->summary($faq->answer),
                'meta' => 'FAQ' . ($faq->is_published ? '' : ' · Draft'),
                'url' => $this->routeUrl('help.support', ['search' => $faq->question], '/dashboard/help-support'),
                'icon' => 'fas fa-circle-question',
            ])
            ->all();

        return $this->group('faqs', 'FAQs', 'fas fa-circle-question', $items);
    }

    private function community(string $term, string $like, ?User $user, int $limit): array
    {
        if (!Schema::hasTable('forum_threads')) {
            return $this->group('community', 'Community', 'fas fa-comments', []);
        }

        $query = DB::table('forum_threads')
            ->leftJoin('users', 'users.id', '=', 'forum_threads.user_id')
            ->select([
                'forum_threads.id',
                'forum_threads.title',
                'forum_threads.content',
                'forum_threads.category',
                'forum_threads.status',
                'users.name as user_name',
            ]);

        $this->whereLike($query, [
            'forum_threads.title',
            'forum_threads.content',
            'forum_threads.category',
            'users.name',
        ], $like);

        if (!$this->canManageSystem($user)) {
            $query->where('forum_threads.status', 'approved');
        }

        $items = $query
            ->orderByDesc('forum_threads.updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($thread) => [
                'title' => $thread->title,
                'description' => $this->summary($thread->content),
                'meta' => trim('Community · ' . ucfirst($thread->category ?: 'discussion') . ($thread->user_name ? ' · ' . $thread->user_name : '')),
                'url' => $this->routeUrl('community.forums', ['search' => $thread->title], '/community/forums'),
                'icon' => 'fas fa-comments',
            ])
            ->all();

        return $this->group('community', 'Community', 'fas fa-comments', $items);
    }

    private function visibleCourseContent(Builder $query, ?User $user, bool $requireEnrollment = true): void
    {
        if ($this->canManageCourses($user)) {
            return;
        }

        $query->where('courses.is_published', true)->where('courses.is_approved', true);

        if ($requireEnrollment && $user && Schema::hasTable('course_enrollments')) {
            $query->whereExists(function ($subQuery) use ($user) {
                $subQuery
                    ->selectRaw('1')
                    ->from('course_enrollments')
                    ->whereColumn('course_enrollments.course_id', 'courses.id')
                    ->where('course_enrollments.user_id', $user->id);
            });
        }
    }

    private function whereLike(Builder $query, array $columns, string $like): void
    {
        $query->where(function (Builder $innerQuery) use ($columns, $like) {
            foreach ($columns as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $innerQuery->{$method}($column, 'like', $like);
            }
        });
    }

    private function group(string $type, string $label, string $icon, array $items): array
    {
        return [
            'type' => $type,
            'label' => $label,
            'icon' => $icon,
            'items' => array_values($items),
        ];
    }

    private function summary(?string $value): string
    {
        return Str::limit(Str::squish(strip_tags((string) $value)), 140);
    }

    private function routeUrl(string $name, array $parameters = [], string $fallback = '/dashboard'): string
    {
        if (Route::has($name)) {
            return route($name, $parameters);
        }

        return url($fallback);
    }

    private function canManageCourses(?User $user): bool
    {
        return $user && method_exists($user, 'canManageCourses') && $user->canManageCourses();
    }

    private function canManageCertificates(?User $user): bool
    {
        return $user && method_exists($user, 'canManageCertificates') && $user->canManageCertificates();
    }

    private function canManageContent(?User $user): bool
    {
        return $this->hasAnyRole($user, ['super_admin', 'academy_admin', 'content_editor']);
    }

    private function canManageMarketplace(?User $user): bool
    {
        return $this->hasAnyRole($user, ['super_admin', 'academy_admin', 'instructor']);
    }

    private function canManageSystem(?User $user): bool
    {
        return $this->hasAnyRole($user, ['super_admin', 'academy_admin']);
    }

    private function hasAnyRole(?User $user, array $roles): bool
    {
        return $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole($roles);
    }
}
