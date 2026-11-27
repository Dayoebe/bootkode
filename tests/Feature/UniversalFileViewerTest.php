<?php

namespace Tests\Feature;

use App\Models\Assessment\LearningSession;
use App\Models\Community\SupportTicket;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\LearningMaterial;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class UniversalFileViewerTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_preview_a_lesson_pdf_with_inline_and_range_headers(): void
    {
        Storage::fake('public');
        [$student, , $lesson] = $this->courseFixture();
        $path = 'lessons/documents/guide.pdf';
        Storage::disk('public')->put($path, $this->pdfFixture());
        $lesson->update(['documents' => [['path' => $path, 'name' => 'Practical guide.pdf', 'size' => Storage::disk('public')->size($path), 'type' => 'pdf']]]);

        $parameters = ['lesson-document', $lesson->id, 0];
        $this->actingAs($student)->get(route('files.view', $parameters))
            ->assertOk()
            ->assertSee('Practical guide.pdf')
            ->assertSee('data-kind="pdf"', false);

        $this->actingAs($student)->withHeader('Range', 'bytes=0-9')->get(route('files.content', $parameters))
            ->assertStatus(206)
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Accept-Ranges', 'bytes')
            ->assertHeader('Content-Range');
    }

    public function test_unenrolled_student_cannot_view_or_stream_a_lesson_file(): void
    {
        Storage::fake('public');
        [, , $lesson] = $this->courseFixture();
        $path = 'lessons/documents/private.pdf';
        Storage::disk('public')->put($path, $this->pdfFixture());
        $lesson->update(['documents' => [['path' => $path, 'name' => 'private.pdf', 'size' => 100, 'type' => 'pdf']]]);
        $other = User::factory()->create();
        $parameters = ['lesson-document', $lesson->id, 0];

        $this->actingAs($other)->get(route('files.view', $parameters))->assertForbidden();
        $this->actingAs($other)->get(route('files.content', $parameters))->assertForbidden();
        $this->actingAs($other)->get(route('files.download', $parameters))->assertForbidden();
    }

    public function test_file_index_and_storage_prefix_are_bounded_to_the_authorized_resource(): void
    {
        Storage::fake('public');
        [$student, , $lesson] = $this->courseFixture();
        Storage::disk('public')->put('unrelated/secret.pdf', $this->pdfFixture());
        $lesson->update(['documents' => [['path' => 'unrelated/secret.pdf', 'name' => 'secret.pdf', 'size' => 100, 'type' => 'pdf']]]);

        $this->actingAs($student)->get(route('files.view', ['lesson-document', $lesson->id, 0]))->assertNotFound();
        $this->actingAs($student)->get(route('files.view', ['lesson-document', $lesson->id, 99]))->assertNotFound();
    }

    public function test_mime_mismatch_is_never_previewed_but_original_remains_an_attachment(): void
    {
        Storage::fake('public');
        [$student, , $lesson] = $this->courseFixture();
        $path = 'lessons/documents/not-really.pdf';
        Storage::disk('public')->put($path, 'plain text pretending to be a PDF');
        $lesson->update(['documents' => [['path' => $path, 'name' => 'not-really.pdf', 'size' => 32, 'type' => 'pdf']]]);
        $parameters = ['lesson-document', $lesson->id, 0];

        $this->actingAs($student)->get(route('files.view', $parameters))
            ->assertOk()
            ->assertSee('stored file contents do not match');
        $this->actingAs($student)->get(route('files.content', $parameters))->assertStatus(415);
        $this->actingAs($student)->get(route('files.download', $parameters))
            ->assertOk()
            ->assertDownload('not-really.pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_legacy_office_format_has_an_honest_download_fallback(): void
    {
        Storage::fake('public');
        [$student, , $lesson] = $this->courseFixture();
        $path = 'lessons/documents/legacy.doc';
        Storage::disk('public')->put($path, random_bytes(64));
        $lesson->update(['documents' => [['path' => $path, 'name' => 'legacy.doc', 'size' => 64, 'type' => 'doc']]]);
        $parameters = ['lesson-document', $lesson->id, 0];

        $this->actingAs($student)->get(route('files.view', $parameters))
            ->assertOk()
            ->assertSee('Legacy Word files cannot be rendered reliably');
        $this->actingAs($student)->get(route('files.content', $parameters))->assertStatus(415);
    }

    public function test_openxml_documents_present_their_installed_renderers(): void
    {
        Storage::fake('public');
        [$student, , $lesson] = $this->courseFixture();
        $formats = [
            ['docx', 'word/document.xml', 'document'],
            ['pptx', 'ppt/presentation.xml', 'presentation'],
            ['xlsx', 'xl/workbook.xml', 'spreadsheet'],
        ];
        $documents = [];
        foreach ($formats as [$extension, $requiredEntry]) {
            $path = 'lessons/documents/sample.'.$extension;
            $this->openXmlFixture(Storage::disk('public')->path($path), $requiredEntry);
            $documents[] = ['path' => $path, 'name' => 'sample.'.$extension, 'size' => Storage::disk('public')->size($path), 'type' => $extension];
        }
        $lesson->update(['documents' => $documents]);

        foreach ($formats as $index => [, , $kind]) {
            $this->actingAs($student)->get(route('files.view', ['lesson-document', $lesson->id, $index]))
                ->assertOk()
                ->assertSee('data-kind="'.$kind.'"', false);
        }
    }

    public function test_svg_preview_removes_active_and_external_content(): void
    {
        Storage::fake('public');
        [$student, , $lesson] = $this->courseFixture();
        $path = 'lessons/images/diagram.svg';
        Storage::disk('public')->put($path, '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><script>alert(1)</script><image href="https://example.test/tracker.png"/><rect width="20" height="20"/></svg>');
        $lesson->update(['images' => [['path' => $path, 'name' => 'diagram.svg', 'size' => 200]]]);

        $response = $this->actingAs($student)->get(route('files.content', ['lesson-image', $lesson->id, 0]));
        $response->assertOk()->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8');
        $this->assertStringNotContainsStringIgnoringCase('<script', $response->getContent());
        $this->assertStringNotContainsStringIgnoringCase('onload=', $response->getContent());
        $this->assertStringNotContainsString('https://example.test', $response->getContent());
        $this->assertStringContainsString('<rect', $response->getContent());
    }

    public function test_private_assessment_draft_is_scoped_to_its_learner(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $fileId = (string) Str::uuid();
        $path = 'private/assessment-submissions/1/10/'.$fileId.'.txt';
        Storage::disk('local')->put($path, 'private draft answer');
        $session = LearningSession::create([
            'user_id' => $owner->id,
            'activity_type' => 'assessment',
            'started_at' => now(),
            'metadata' => ['submission_files' => [10 => [[
                'id' => $fileId, 'path' => $path, 'name' => 'answer.txt', 'size' => 20,
                'sha256' => hash_file('sha256', Storage::disk('local')->path($path)),
            ]]]],
        ]);
        $parameters = ['assessment-submission', $session->id, $fileId];

        $this->actingAs($owner)->get(route('files.view', $parameters))->assertOk()->assertSee('answer.txt');
        $this->actingAs($other)->get(route('files.view', $parameters))->assertForbidden();
    }

    public function test_support_attachment_is_visible_only_to_its_owner(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $path = 'private/support-ticket-attachments/evidence.txt';
        Storage::disk('local')->put($path, 'support evidence');
        $ticket = SupportTicket::create(['user_id' => $owner->id, 'subject' => 'Viewer issue', 'description' => 'Evidence attached.', 'attachment' => $path, 'status' => 'open']);

        $this->actingAs($owner)->get(route('files.view', ['support-attachment', $ticket->id, 0]))->assertOk();
        $this->actingAs($other)->get(route('files.view', ['support-attachment', $ticket->id, 0]))->assertForbidden();
    }

    public function test_learning_material_visibility_and_missing_file_are_enforced(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $material = LearningMaterial::create([
            'title' => 'Private workbook', 'type' => 'worksheet', 'created_by' => $owner->id,
            'file_path' => 'learning-materials/workbook.csv', 'file_size' => 12,
            'file_type' => 'text/csv', 'original_filename' => 'workbook.csv',
            'is_public' => false, 'difficulty_level' => 'beginner', 'status' => 'published',
        ]);
        Storage::disk('public')->put($material->file_path, "Topic,Score\nRouting,8");

        $this->actingAs($other)->get(route('files.view', ['learning-material', $material->id, 0]))->assertForbidden();
        $this->actingAs($owner)->get(route('files.view', ['learning-material', $material->id, 0]))->assertOk();
        Storage::disk('public')->delete($material->file_path);
        $this->actingAs($owner)->get(route('files.view', ['learning-material', $material->id, 0]))->assertNotFound();
    }

    private function courseFixture(): array
    {
        $instructor = User::factory()->create();
        $student = User::factory()->create();
        $course = Course::create([
            'instructor_id' => $instructor->id, 'title' => 'Secure file learning',
            'slug' => 'secure-file-learning-'.Str::lower(Str::random(6)), 'description' => 'File preview test course.',
            'difficulty_level' => 'beginner', 'is_published' => true, 'is_approved' => true,
            'is_free' => true, 'published_at' => now(),
        ]);
        $section = Section::create(['course_id' => $course->id, 'title' => 'Resources', 'order' => 1, 'is_locked' => false]);
        $lesson = Lesson::create(['section_id' => $section->id, 'title' => 'Read the resources', 'description' => 'Authorized files.', 'order' => 1, 'published_at' => now()]);
        CourseEnrollment::create(['course_id' => $course->id, 'user_id' => $student->id, 'enrolled_at' => now()]);

        return [$student, $course, $lesson];
    }

    private function pdfFixture(): string
    {
        return "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 0/Kids[]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";
    }

    private function openXmlFixture(string $path, string $requiredEntry): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>');
        $zip->addFromString($requiredEntry, '<?xml version="1.0"?><root/>');
        $zip->close();
    }
}
