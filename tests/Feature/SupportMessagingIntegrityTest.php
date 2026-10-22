<?php

namespace Tests\Feature;

use App\Livewire\SystemManagement\HelpSupport;
use App\Livewire\SystemManagement\SupportTicketManagement;
use App\Livewire\Messaging\DirectMessages;
use App\Models\Community\SupportTicket;
use App\Models\Community\Faq;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Messaging\DirectConversation;
use App\Models\Messaging\DirectMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupportMessagingIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_cannot_read_or_mount_support_administration(): void
    {
        $this->actingAs(User::factory()->create());
        foreach ([SupportTicketManagement::class, \App\Livewire\SystemManagement\FeedbackManagement::class, \App\Livewire\SystemManagement\FaqManagement::class] as $component) Livewire::test($component)->assertForbidden();
        $this->get(route('support.tickets'))->assertForbidden();
    }

    public function test_support_and_faq_search_preserve_owner_and_publication_scope(): void
    {
        $owner = User::factory()->create();
        SupportTicket::create(['user_id' => $owner->id, 'subject' => 'PRIVATE other ticket', 'description' => 'search-secret', 'status' => 'open']);
        Faq::create(['question' => 'PRIVATE draft answer', 'answer' => 'search-secret', 'is_published' => false]);
        Livewire::actingAs(User::factory()->create())->test(HelpSupport::class)
            ->set('searchFaq', 'search-secret')->assertDontSee('PRIVATE draft answer')
            ->set('activeTab', 'ticket_history')->set('ticketSearch', 'search-secret')->assertDontSee('PRIVATE other ticket');
    }

    public function test_ticket_upload_is_private_and_download_checks_owner(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        Livewire::actingAs($owner)->test(HelpSupport::class)->set('subject', 'Account question')
            ->set('description', 'Please help with my account settings.')
            ->set('attachment', UploadedFile::fake()->create('details.pdf', 10, 'application/pdf'))
            ->call('submitTicket')->assertHasNoErrors()->assertSee('Your support ticket was submitted.');
        $ticket = SupportTicket::firstOrFail();
        $this->assertStringStartsWith('private/support-ticket-attachments/', $ticket->attachment);
        Storage::disk('local')->assertExists($ticket->attachment);
        $this->get(route('support.attachment', $ticket))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('support.attachment', $ticket))->assertForbidden();
    }

    public function test_admin_response_persists_and_invalid_status_is_rejected(): void
    {
        Notification::fake();
        Role::findOrCreate('academy_admin', 'web');
        $admin = User::factory()->create(['role' => 'academy_admin']);
        $ticket = SupportTicket::create(['user_id' => User::factory()->create()->id, 'subject' => 'Ticket fixture', 'description' => 'Private request', 'status' => 'open']);
        Livewire::actingAs($admin)->test(SupportTicketManagement::class)->call('openTicket', $ticket->id)
            ->set('response', 'Your account settings are ready for review.')->set('resolutionStatus', 'resolved')
            ->call('respondToTicket')->assertHasNoErrors()->assertSet('showResponseModal', false)
            ->call('updateStatus', $ticket->id, 'invented')->assertHasErrors('status');
        $this->assertSame('resolved', $ticket->fresh()->status);
        $this->assertSame($admin->id, $ticket->fresh()->responded_by);
        $this->assertStringContainsString('ready for review', $ticket->fresh()->response);
    }

    public function test_forged_conversation_cannot_mark_other_messages_read_or_send(): void
    {
        $student = User::factory()->create();
        $instructor = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => $instructor->id, 'title' => 'Message fixture', 'slug' => 'message-fixture', 'description' => 'Private course']));
        $conversation = DirectConversation::create(['course_id' => $course->id, 'student_id' => $student->id, 'instructor_id' => $instructor->id]);
        $message = DirectMessage::create(['direct_conversation_id' => $conversation->id, 'sender_id' => $instructor->id, 'body' => 'PRIVATE conversation content']);
        Livewire::actingAs(User::factory()->create())->test(DirectMessages::class)->set('activeConversationId', $conversation->id)
            ->call('pollMessages')->assertDontSee('PRIVATE conversation content')
            ->set('messageBody', 'Injected outsider message')->call('sendMessage')->assertHasErrors('messageBody');
        $this->assertNull($message->fresh()->read_at);
        $this->assertDatabaseCount('direct_messages', 1);
    }
    public function test_feedback_search_does_not_escape_owner_scope_through_course_title(): void
    {
        $owner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => $owner->id, 'title' => 'search-secret', 'slug' => 'feedback-search', 'description' => 'Course fixture']));
        \App\Models\Community\Feedback::create(['user_id' => $owner->id, 'course_id' => $course->id, 'category' => 'course', 'message' => 'PRIVATE other feedback', 'rating' => 3, 'status' => 'open']);
        Livewire::actingAs(User::factory()->create())->test(\App\Livewire\SystemManagement\Feedback::class)->set('activeTab', 'my_feedback')->set('search', 'search-secret')->assertDontSee('PRIVATE other feedback');
    }

    public function test_admin_can_use_the_real_feedback_response_action(): void
    {
        Notification::fake();
        Role::findOrCreate('academy_admin', 'web');
        $admin = User::factory()->create(['role' => 'academy_admin']);
        $feedback = \App\Models\Community\Feedback::create(['user_id' => User::factory()->create()->id, 'category' => 'general', 'message' => 'Feedback fixture', 'rating' => 4, 'status' => 'open']);
        Livewire::actingAs($admin)->test(\App\Livewire\SystemManagement\FeedbackManagement::class)->call('openFeedback', $feedback->id)
            ->set('response', 'Thank you. We have recorded the issue.')->call('respond')->assertHasNoErrors()->assertSet('showResponseModal', false);
        $this->assertSame('responded', $feedback->fresh()->status);
        $this->assertSame($admin->id, $feedback->fresh()->responded_by);
    }

    public function test_feedback_rejects_a_forged_private_course_selection(): void
    {
        $owner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => $owner->id, 'title' => 'Private draft', 'slug' => 'private-feedback-draft', 'is_published' => false]));
        Livewire::actingAs(User::factory()->create())->test(\App\Livewire\SystemManagement\Feedback::class)
            ->set('category', 'course')->set('course_id', $course->id)->set('message', 'A forged draft selection')->set('rating', 4)
            ->call('saveFeedback')->assertHasErrors('course_id');
        $this->assertDatabaseCount('feedback', 0);
    }

}
