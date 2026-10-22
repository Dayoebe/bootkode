<?php

namespace Tests\Feature;

use App\Livewire\CertificateManagement\CertificateTemplates;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Credentials\CertificateTemplate;
use App\Models\Learning\Course;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CertificateTemplatesTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        Role::findOrCreate($role, 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function savedTemplate(string $name = 'Saved design'): CertificateTemplate
    {
        return app(CertificateService::class)->saveTemplate([
            'name' => $name, 'description' => 'A real saved design',
            'settings' => CertificateTemplate::DEFAULT_SETTINGS,
        ]);
    }

    public function test_creation_and_editing_persist_after_remount_and_deletion_removes_record(): void
    {
        $user = $this->userWithRole('academy_admin');
        Livewire::actingAs($user)->test(CertificateTemplates::class)
            ->call('showCreateModal')->set('templateName', 'Practical skills')
            ->set('accentColor', '#123456')->call('saveTemplate')
            ->assertHasNoErrors()->assertSet('showEditor', false);
        $template = CertificateTemplate::sole();
        $this->assertSame($user->id, $template->created_by);
        $this->assertSame('#123456', $template->settings['accentColor']);
        Livewire::test(CertificateTemplates::class)->assertSee('Practical skills')
            ->call('showEditModal', $template->id)->assertSet('accentColor', '#123456')
            ->set('templateName', 'Updated design')->call('updateTemplate')->assertHasNoErrors();
        $this->assertSame('Updated design', $template->fresh()->name);
        Livewire::test(CertificateTemplates::class)->call('deleteTemplate', $template->id);
        $this->assertDatabaseCount('certificate_templates', 0);
    }

    public function test_default_switch_is_persistent_and_default_deletion_is_blocked(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $first = $this->savedTemplate('First design');
        $next = $this->savedTemplate('Next design');
        $component = Livewire::test(CertificateTemplates::class);
        $component->call('setDefaultTemplate', $first->id)->call('setDefaultTemplate', $first->id);
        $this->assertTrue($first->fresh()->is_default);
        $component->call('deleteTemplate', $first->id)->assertHasErrors('template');
        $this->assertDatabaseCount('certificate_templates', 2);
        $component->call('setDefaultTemplate', $next->id);
        $this->assertFalse($first->fresh()->is_default);
        $this->assertTrue($next->fresh()->is_default);
        $this->assertSame(1, CertificateTemplate::where('default_slot', 1)->count());
        $component->call('deleteTemplate', $first->id);
        $this->assertDatabaseCount('certificate_templates', 1);
    }

    public function test_learner_cannot_open_template_management(): void
    {
        Livewire::actingAs($this->userWithRole('student'))->test(CertificateTemplates::class)->assertForbidden();
        $this->get(route('admin.certificates.templates'))->assertForbidden();
    }

    public function test_instructor_can_preview_but_cannot_mutate_templates(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $template = $this->savedTemplate();
        $instructor = $this->userWithRole('instructor');
        Livewire::actingAs($instructor)->test(CertificateTemplates::class)
            ->call('previewTemplate', $template->id)->assertSet('showPreview', true)
            ->call('deleteTemplate', $template->id)->assertForbidden();
        Livewire::test(CertificateTemplates::class)->set('templateName', 'Forged design')
            ->call('saveTemplate')->assertForbidden();
        Livewire::test(CertificateTemplates::class)->call('showEditModal', $template->id)->assertForbidden();
        $this->assertDatabaseCount('certificate_templates', 1);
    }

    public function test_academy_admin_cannot_assign_a_default_by_action_or_forged_form(): void
    {
        $this->actingAs($this->userWithRole('academy_admin'));
        $template = $this->savedTemplate();
        Livewire::test(CertificateTemplates::class)->call('setDefaultTemplate', $template->id)->assertForbidden();
        Livewire::test(CertificateTemplates::class)->set('templateName', 'Forged default')
            ->set('isDefault', true)->call('saveTemplate')->assertForbidden();
        $this->assertDatabaseCount('certificate_templates', 1);
        $this->assertFalse($template->fresh()->is_default);
    }

    public function test_admin_can_edit_an_existing_default_without_changing_its_assignment(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $template = $this->savedTemplate();
        app(CertificateService::class)->setDefaultTemplate($template->id);
        Livewire::actingAs($this->userWithRole('academy_admin'))->test(CertificateTemplates::class)
            ->call('showEditModal', $template->id)->set('templateName', 'Corrected name')
            ->call('updateTemplate')->assertHasNoErrors();
        $this->assertTrue($template->fresh()->is_default);
    }

    public function test_missing_selection_does_not_clear_the_current_default(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $template = $this->savedTemplate();
        app(CertificateService::class)->setDefaultTemplate($template->id);
        Livewire::test(CertificateTemplates::class)->call('setDefaultTemplate', $template->id + 999)->assertNotFound();
        $this->assertTrue($template->fresh()->is_default);
    }

    public function test_unsafe_styles_and_unsupported_fonts_are_rejected(): void
    {
        Livewire::actingAs($this->userWithRole('super_admin'))->test(CertificateTemplates::class)
            ->set('templateName', 'Unsafe design')->set('accentColor', '#fff; background:url(https://example.test)')
            ->set('headerFont', '</style><script>alert(1)</script>')->call('saveTemplate')
            ->assertHasErrors(['accentColor', 'headerFont']);
        $this->assertDatabaseCount('certificate_templates', 0);
    }

    public function test_preview_uses_selected_settings_and_has_no_issued_credentials_or_download_link(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $template = $this->savedTemplate();
        $template->update(['settings' => array_replace($template->settings, ['accentColor' => '#123456'])]);
        $component = Livewire::test(CertificateTemplates::class)->call('previewTemplate', $template->id)
            ->assertSet('showPreview', true);
        $html = $component->get('previewHtml');
        $this->assertStringContainsString('#123456', $html);
        $this->assertStringContainsString('DESIGN PREVIEW', $html);
        $this->assertStringContainsString('Recipient name', $html);
        $this->assertStringNotContainsString('Download PDF', $html);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_issued_certificate_retains_its_design_after_template_edit_switch_and_deletion(): void
    {
        Notification::fake();
        $user = $this->userWithRole('super_admin');
        $this->actingAs($user);
        $template = $this->savedTemplate('Original design');
        app(CertificateService::class)->setDefaultTemplate($template->id);
        $course = Course::withoutEvents(fn () => Course::create([
            'title' => 'Certificate fixture', 'slug' => 'certificate-template-fixture', 'instructor_id' => $user->id,
        ]));
        $certificate = Certificate::create([
            'user_id' => $user->id, 'course_id' => $course->id, 'status' => 'requested',
            'metadata' => ['existing' => 'preserved'],
        ]);
        $this->partialMock(CertificateService::class)->shouldReceive('generateCertificateAssets')->once()->andReturn(true);
        $certificate->approve($user->id);
        $snapshot = $certificate->fresh()->metadata['template_snapshot'];
        $this->assertSame('Original design', $snapshot['name']);
        $this->assertSame('preserved', $certificate->metadata['existing']);
        $template->update(['settings' => array_replace($template->settings, ['accentColor' => '#123456'])]);
        $replacement = $this->savedTemplate('Replacement design');
        app(CertificateService::class)->setDefaultTemplate($replacement->id);
        app(CertificateService::class)->deleteTemplate($template->id);
        $this->assertSame($snapshot, $certificate->fresh()->metadata['template_snapshot']);
        $this->get(route('certificate.view', $certificate->verification_code))
            ->assertOk()->assertSee('#0f766e', false)->assertDontSee('#123456', false);
    }

    public function test_legacy_issued_certificate_does_not_pick_up_the_new_default(): void
    {
        $user = $this->userWithRole('super_admin');
        $this->actingAs($user);
        $template = $this->savedTemplate();
        app(CertificateService::class)->setDefaultTemplate($template->id);
        $course = Course::withoutEvents(fn () => Course::create([
            'title' => 'Historical course', 'slug' => 'historical-template-fixture', 'instructor_id' => $user->id,
        ]));
        $certificate = Certificate::create(['user_id' => $user->id, 'course_id' => $course->id, 'status' => 'issued']);
        $this->get(route('certificate.view', $certificate->verification_code))
            ->assertOk()->assertSee('#b8860b', false)->assertDontSee('#0f766e', false)->assertSee('Not recorded');
        $this->assertNull($certificate->fresh()->metadata);
    }
}
