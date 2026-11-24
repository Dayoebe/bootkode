<?php

namespace Tests\Feature;

use App\Livewire\Blog\AdminBlogCategories;
use App\Livewire\Blog\AdminBlogPosts;
use App\Livewire\Content\Partial\CreateDocument;
use App\Livewire\Newsletter\Partials\Settings as NewsletterSettings;
use App\Livewire\Pages\Partials\Analytics as PageAnalytics;
use App\Livewire\Pages\Partials\CreatePage;
use App\Models\Core\User;
use App\Services\Authorization\PublishingAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublishingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishing_access_matrix_is_explicit_for_every_supported_ability(): void
    {
        $access = app(PublishingAccess::class);

        $expectations = [
            PublishingAccess::BLOG_POSTS => ['super_admin', 'academy_admin', 'content_editor'],
            PublishingAccess::BLOG_CATEGORIES => ['super_admin', 'academy_admin'],
            PublishingAccess::BLOG_COMMENTS => ['super_admin', 'academy_admin', 'content_editor'],
            PublishingAccess::BLOG_SETTINGS => ['super_admin'],
            PublishingAccess::BLOG_SEO => ['super_admin', 'content_editor'],
            PublishingAccess::PAGE_CONTENT => ['super_admin', 'academy_admin', 'content_editor'],
            PublishingAccess::PAGE_ANALYTICS => ['super_admin', 'academy_admin'],
            PublishingAccess::PAGE_SEO => ['super_admin', 'academy_admin'],
            PublishingAccess::PAGE_SETTINGS => ['super_admin'],
            PublishingAccess::CONTENT_EDITOR => ['super_admin', 'academy_admin', 'content_editor'],
            PublishingAccess::CONTENT_ADMINISTRATION => ['super_admin', 'academy_admin'],
            PublishingAccess::NEWSLETTER_EDITOR => ['super_admin', 'academy_admin', 'content_editor'],
            PublishingAccess::NEWSLETTER_SETTINGS => ['super_admin'],
        ];

        foreach ($expectations as $ability => $roles) {
            $this->assertSame($roles, $access->rolesFor($ability), $ability);
        }
    }

    public function test_learner_is_denied_every_publishing_administration_route(): void
    {
        $this->actingAs($this->userWithRole(User::ROLE_STUDENT));

        foreach ([
            '/admin/blog/posts', '/admin/blog/categories', '/admin/blog/comments', '/admin/blog/settings', '/admin/blog/seo',
            '/admin/pages', '/admin/pages/analytics', '/admin/pages/seo', '/admin/pages/settings',
            '/content', '/content/create-document', '/content/categories', '/content/settings',
            '/dashboard/newsletter', '/dashboard/newsletter/settings',
        ] as $uri) {
            $this->get($uri)->assertForbidden();
        }
    }

    public function test_content_editor_http_scope_matches_the_advertised_publishing_roles(): void
    {
        $this->actingAs($this->userWithRole(User::ROLE_CONTENT_EDITOR));

        $this->get('/admin/blog/posts')->assertOk();
        $this->get('/admin/blog/seo')->assertOk();
        $this->get('/admin/blog/categories')->assertForbidden();
        $this->get('/admin/blog/settings')->assertForbidden();
        $this->get('/admin/pages/analytics')->assertForbidden();
        $this->get('/admin/pages/settings')->assertForbidden();
        $this->get('/content/categories')->assertForbidden();
        $this->get('/dashboard/newsletter/settings')->assertForbidden();
    }

    public function test_components_recheck_roles_on_direct_and_subsequent_livewire_requests(): void
    {
        $learner = $this->userWithRole(User::ROLE_STUDENT);
        foreach ([
            AdminBlogPosts::class,
            \App\Livewire\Blog\AdminBlogPostForm::class,
            AdminBlogCategories::class,
            \App\Livewire\Blog\AdminBlogComments::class,
            \App\Livewire\Blog\AdminBlogSettings::class,
            \App\Livewire\Pages\PageManager::class,
            CreatePage::class,
            \App\Livewire\Pages\Partials\PagesList::class,
            \App\Livewire\Pages\Partials\MediaManager::class,
            \App\Livewire\Pages\Partials\Templates::class,
            PageAnalytics::class,
            \App\Livewire\Pages\Partials\SeoManager::class,
            \App\Livewire\Pages\Partials\Settings::class,
            \App\Livewire\Content\ContentDocumentationCenter::class,
            \App\Livewire\Content\Partial\AllDocuments::class,
            \App\Livewire\Content\Partial\ContentModeration::class,
            CreateDocument::class,
            \App\Livewire\Content\Partial\DocumentCategories::class,
            \App\Livewire\Content\Partial\DocumentReviews::class,
            \App\Livewire\Content\Partial\DocumentSettings::class,
            \App\Livewire\Content\Partial\Documentation::class,
            \App\Livewire\Content\Partial\LearningMaterials::class,
            \App\Livewire\Content\Partial\Localization::class,
            \App\Livewire\Content\Partial\VideoLibrary::class,
            \App\Livewire\Newsletter\NewsletterCenter::class,
            \App\Livewire\Newsletter\Partials\Analytics::class,
            \App\Livewire\Newsletter\Partials\CampaignManagement::class,
            \App\Livewire\Newsletter\Partials\CampaignReports::class,
            \App\Livewire\Newsletter\Partials\PerformanceOptimization::class,
            NewsletterSettings::class,
            \App\Livewire\Newsletter\Partials\SubscriberManagement::class,
            \App\Livewire\Newsletter\Partials\TemplateManagement::class,
        ] as $component) {
            Livewire::actingAs($learner)->test($component)->assertForbidden();
        }

        $editor = $this->userWithRole(User::ROLE_CONTENT_EDITOR);
        Livewire::actingAs($editor)->test(AdminBlogCategories::class)->assertForbidden();
        Livewire::actingAs($editor)->test(PageAnalytics::class)->assertForbidden();
        Livewire::actingAs($editor)->test(NewsletterSettings::class)->assertForbidden();

        $component = Livewire::actingAs($editor)->test(AdminBlogPosts::class);
        $editor->syncRoles(User::ROLE_STUDENT);
        $component->call('updatedBulkActions')->assertForbidden();
    }

    public function test_blog_upload_requires_an_authorized_staff_role(): void
    {
        Storage::fake('public');

        $this->post(route('api.blog.upload.image'), [
            'image' => UploadedFile::fake()->image('guest.jpg'),
        ])->assertRedirect(route('login'));

        $this->actingAs($this->userWithRole(User::ROLE_STUDENT))
            ->post(route('api.blog.upload.image'), [
                'image' => UploadedFile::fake()->image('learner.jpg'),
            ])->assertForbidden();

        $response = $this->actingAs($this->userWithRole(User::ROLE_CONTENT_EDITOR))
            ->post(route('api.blog.upload.image'), [
                'image' => UploadedFile::fake()->image('editor.jpg'),
            ]);

        $response->assertOk()->assertJson(['success' => true]);
        Storage::disk('public')->assertExists($response->json('path'));
    }

    private function userWithRole(string $role): User
    {
        Role::findOrCreate($role, 'web');
        $user = User::factory()->create(['role' => $role]);
        $user->assignRole($role);

        return $user;
    }
}
