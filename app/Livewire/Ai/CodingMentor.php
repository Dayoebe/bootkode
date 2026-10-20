<?php

namespace App\Livewire\Ai;

use App\Exceptions\AiProviderException;
use App\Models\Ai\AiConversation;
use App\Models\Learning\CourseEnrollment;
use App\Services\Ai\AiManager;
use App\Services\Ai\CodingMentorService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'BootKode AI Coding Mentor', 'description' => 'Private, course-aware programming guidance', 'icon' => 'fas fa-robot', 'active' => 'ai.coding-mentor'])]
class CodingMentor extends Component
{
    use WithPagination;

    public ?int $activeConversationId = null;

    public string $question = '';

    public string $newTitle = '';

    public string $selectedCourseId = '';

    public ?string $providerError = null;

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()->role, [
            \App\Models\Core\User::ROLE_STUDENT,
            \App\Models\Core\User::ROLE_INSTRUCTOR,
            \App\Models\Core\User::ROLE_ACADEMY_ADMIN,
            \App\Models\Core\User::ROLE_SUPER_ADMIN,
        ], true), 403);

        $this->activeConversationId = AiConversation::where('user_id', Auth::id())->latest('last_message_at')->latest('id')->value('id');
    }

    public function newConversation(CodingMentorService $mentor): void
    {
        $courseId = $this->selectedCourseId !== '' ? (int) $this->selectedCourseId : null;
        $conversation = $mentor->createConversation(Auth::user(), $courseId);
        $this->activeConversationId = $conversation->id;
        $this->question = '';
        $this->providerError = null;
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($conversationId);
        $this->activeConversationId = $conversation->id;
        $this->selectedCourseId = $conversation->course_id ? (string) $conversation->course_id : '';
        $this->providerError = null;
    }

    public function renameConversation(CodingMentorService $mentor): void
    {
        $this->validate(['newTitle' => 'required|string|max:120']);
        $conversation = AiConversation::findOrFail($this->activeConversationId);
        $mentor->rename(Auth::user(), $conversation, $this->newTitle);
        $this->newTitle = '';
    }

    public function deleteConversation(CodingMentorService $mentor): void
    {
        if (! $this->activeConversationId) {
            return;
        }
        $conversation = AiConversation::findOrFail($this->activeConversationId);
        $mentor->delete(Auth::user(), $conversation);
        $this->activeConversationId = AiConversation::where('user_id', Auth::id())->latest('last_message_at')->latest('id')->value('id');
        $this->question = '';
    }

    public function send(CodingMentorService $mentor): void
    {
        $this->validate(['question' => 'required|string|max:'.max(200, (int) config('bootkode_ai.max_input_characters', 6000))]);
        if (! $this->activeConversationId) {
            $this->newConversation($mentor);
        }
        $conversation = AiConversation::findOrFail($this->activeConversationId);
        try {
            $mentor->send(Auth::user(), $conversation, $this->question);
            $this->question = '';
            $this->providerError = null;
            $this->dispatch('mentor-answered');
        } catch (AiProviderException $exception) {
            $this->providerError = $exception->getMessage();
        }
    }

    public function render(AiManager $manager)
    {
        $user = Auth::user();
        $conversations = AiConversation::with('course')->where('user_id', $user->id)->latest('last_message_at')->latest('id')->paginate(15);
        $activeConversation = $this->activeConversationId
            ? AiConversation::with(['course', 'messages' => fn ($query) => $query->oldest('id')])->where('user_id', $user->id)->findOrFail($this->activeConversationId)
            : null;
        $courses = CourseEnrollment::with('course')->where('user_id', $user->id)->latest('updated_at')->get()->pluck('course')->filter()->unique('id')->values();

        return view('livewire.ai.coding-mentor', compact('conversations', 'activeConversation', 'courses') + ['providerStatus' => $manager->status()]);
    }
}
