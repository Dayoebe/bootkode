<?php

namespace App\Livewire\Messaging;

use App\Models\Learning\Course;
use App\Models\Messaging\DirectConversation;
use App\Models\Messaging\DirectMessage;
use App\Notifications\DirectMessageReceived;
use App\Services\DirectMessagingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Messages')]
class DirectMessages extends Component
{
    public ?int $activeConversationId = null;
    public string $messageBody = '';
    public string $search = '';
    public int $lastSeenMessageId = 0;

    protected array $rules = [
        'messageBody' => ['required', 'string', 'min:1', 'max:2000'],
    ];

    public function mount($conversation = null): void
    {
        if ($conversation !== null) {
            $conversation = DirectConversation::query()
                ->forParticipant(Auth::user())
                ->findOrFail((int) $conversation);

            $this->activeConversationId = $conversation->id;
            $this->markActiveConversationRead();
            $this->syncLastSeenMessageId();

            return;
        }

        if ($courseKey = request()->query('course')) {
            $course = Course::query()
                ->where('slug', $courseKey)
                ->orWhere('id', $courseKey)
                ->firstOrFail();

            $this->openCourseConversation($course);

            return;
        }

        $this->activeConversationId = $this->conversations->first()?->id;
        $this->markActiveConversationRead();
        $this->syncLastSeenMessageId();
    }

    public function getConversationsProperty()
    {
        $user = Auth::user();

        return DirectConversation::query()
            ->with([
                'course',
                'student',
                'instructor',
                'latestMessage.sender',
            ])
            ->withCount([
                'messages as unread_count' => fn($query) => $query
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at'),
            ])
            ->forParticipant($user)
            ->when($this->search !== '', function ($query) {
                $search = '%' . $this->search . '%';

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('last_message_preview', 'like', $search)
                        ->orWhereHas('course', fn($courseQuery) => $courseQuery->where('title', 'like', $search))
                        ->orWhereHas('student', fn($studentQuery) => $studentQuery->where('name', 'like', $search))
                        ->orWhereHas('instructor', fn($instructorQuery) => $instructorQuery->where('name', 'like', $search));
                });
            })
            ->orderByRaw('COALESCE(last_message_at, updated_at) desc')
            ->limit(60)
            ->get();
    }

    public function getActiveConversationProperty(): ?DirectConversation
    {
        if (!$this->activeConversationId) {
            return null;
        }

        return DirectConversation::query()
            ->with(['course', 'student', 'instructor'])
            ->forParticipant(Auth::user())
            ->find($this->activeConversationId);
    }

    public function getMessagesProperty()
    {
        if (!$this->activeConversationId || !$this->activeConversation) {
            return collect();
        }

        $messageIds = DirectMessage::query()
            ->where('direct_conversation_id', $this->activeConversationId)
            ->latest()
            ->limit(150)
            ->pluck('id');

        return DirectMessage::query()
            ->with('sender')
            ->whereIn('id', $messageIds)
            ->oldest()
            ->get();
    }

    public function getAvailableCoursesProperty()
    {
        $user = Auth::user();

        if (!$user || !$user->enrollments()->exists()) {
            return collect();
        }

        return Course::query()
            ->with('instructor')
            ->whereHas('enrollments', fn($query) => $query->where('user_id', $user->id))
            ->whereNotNull('instructor_id')
            ->where('instructor_id', '!=', $user->id)
            ->orderBy('title')
            ->limit(8)
            ->get();
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = DirectConversation::query()
            ->forParticipant(Auth::user())
            ->findOrFail($conversationId);

        $this->activeConversationId = $conversation->id;
        $this->resetValidation();
        $this->markActiveConversationRead();
        $this->syncLastSeenMessageId();
        $this->dispatch('conversation-opened');
    }

    public function startConversationForCourse(int $courseId): void
    {
        $course = Course::with('instructor')->findOrFail($courseId);
        $this->openCourseConversation($course);
    }

    public function sendMessage(?string $body = null): void
    {
        if ($body !== null) {
            $this->messageBody = $body;
        }

        $this->messageBody = trim($this->messageBody);
        $this->validate();

        $conversation = $this->activeConversation;

        if (!$conversation) {
            $this->addError('messageBody', 'Choose a conversation before sending a message.');
            return;
        }

        app(DirectMessagingService::class)->ensureParticipant(Auth::user(), $conversation);

        $message = DirectMessage::create([
            'direct_conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $this->messageBody,
        ]);

        $conversation->update([
            'last_message_preview' => Str::limit($message->body, 180),
            'last_message_at' => $message->created_at,
        ]);

        $recipient = $conversation->fresh(['student', 'instructor'])->recipientFor(Auth::user());

        if ($recipient) {
            $recipient->notify(new DirectMessageReceived($message));
        }

        $this->messageBody = '';
        $this->resetValidation();
        $this->activeConversationId = $conversation->id;
        $this->lastSeenMessageId = $message->id;
        $this->markActiveConversationRead();
        $this->dispatch('conversation-opened');
    }

    public function pollMessages(): void
    {
        $this->dispatchIncomingMessageNotice();
        $this->markActiveConversationRead();
    }

    private function openCourseConversation(Course $course): void
    {
        try {
            $conversation = app(DirectMessagingService::class)
                ->getOrCreateCourseConversation($course, Auth::user());

            $this->activeConversationId = $conversation->id;
            $this->markActiveConversationRead();
            $this->syncLastSeenMessageId();
            $this->resetValidation();
            $this->dispatch('conversation-opened');
        } catch (AuthorizationException $exception) {
            abort(403, $exception->getMessage());
        } catch (ValidationException $exception) {
            $this->addError('messageBody', collect($exception->errors())->flatten()->first());
        }
    }

    private function markActiveConversationRead(): void
    {
        if (!$this->activeConversationId) {
            return;
        }

        DirectMessage::query()
            ->where('direct_conversation_id', $this->activeConversationId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function dispatchIncomingMessageNotice(): void
    {
        if (!$this->activeConversationId) {
            $this->lastSeenMessageId = 0;
            return;
        }

        $latestMessageId = (int) DirectMessage::query()
            ->where('direct_conversation_id', $this->activeConversationId)
            ->max('id');

        if ($latestMessageId === 0) {
            $this->lastSeenMessageId = 0;
            return;
        }

        if ($latestMessageId <= $this->lastSeenMessageId) {
            return;
        }

        $incomingCount = DirectMessage::query()
            ->where('direct_conversation_id', $this->activeConversationId)
            ->where('id', '>', $this->lastSeenMessageId)
            ->where('sender_id', '!=', Auth::id())
            ->count();

        $this->lastSeenMessageId = $latestMessageId;

        if ($incomingCount > 0) {
            $this->dispatch('incoming-message', count: $incomingCount);
        }
    }

    private function syncLastSeenMessageId(): void
    {
        if (!$this->activeConversationId) {
            $this->lastSeenMessageId = 0;
            return;
        }

        $this->lastSeenMessageId = (int) DirectMessage::query()
            ->where('direct_conversation_id', $this->activeConversationId)
            ->max('id');
    }

    public function render()
    {
        return view('livewire.messaging.direct-messages', [
            'conversations' => $this->conversations,
            'activeConversation' => $this->activeConversation,
            'messages' => $this->messages,
            'availableCourses' => $this->availableCourses,
        ]);
    }
}
