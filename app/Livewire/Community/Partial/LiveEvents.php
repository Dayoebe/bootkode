<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommunityActivity;
use Carbon\Carbon;

class LiveEvents extends Component
{
    use WithPagination;
    
    public $showCreateForm = false;
    public $search = '';
    public $timeFilter = 'upcoming';
    
    // Form properties
    public $title = '';
    public $description = '';
    public $location = '';
    public $startDate = '';
    public $endDate = '';
    public $maxParticipants = '';
    public $tags = '';
    public $eventType = 'webinar';

    protected $rules = [
        'title' => 'required|min:5|max:255',
        'description' => 'required|min:20',
        'location' => 'required|string|max:500',
        'startDate' => 'required|date|after:now',
        'endDate' => 'nullable|date|after:startDate',
        'maxParticipants' => 'nullable|integer|min:1|max:1000',
        'tags' => 'nullable|string',
        'eventType' => 'required|in:webinar,workshop,meetup,presentation',
    ];

    public function createEvent()
    {
        $this->validate();

        $tags = $this->tags ? array_map('trim', explode(',', $this->tags)) : [];
        
        $metadata = [
            'event_type' => $this->eventType,
        ];

        CommunityActivity::create([
            'creator_id' => auth()->id(),
            'type' => 'live_event',
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'max_participants' => $this->maxParticipants,
            'tags' => $tags,
            'metadata' => $metadata,
            'status' => 'active',
        ]);

        $this->reset(['title', 'description', 'location', 'startDate', 'endDate', 'maxParticipants', 'tags', 'eventType', 'showCreateForm']);
        
        session()->flash('message', 'Live event created successfully!');
    }

    public function registerForEvent($eventId)
    {
        $event = CommunityActivity::findOrFail($eventId);
        
        if ($event->join()) {
            session()->flash('message', 'Successfully registered for the event!');
        } else {
            session()->flash('error', 'Unable to register for this event.');
        }
    }

    public function unregisterFromEvent($eventId)
    {
        $event = CommunityActivity::findOrFail($eventId);
        
        if ($event->leave()) {
            session()->flash('message', 'Successfully unregistered from the event.');
        } else {
            session()->flash('error', 'Unable to unregister from this event.');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTimeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $eventsQuery = CommunityActivity::liveEvents()
            ->with(['creator', 'activeParticipants.user'])
            ->when($this->search, function($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->timeFilter === 'upcoming', function($query) {
                return $query->where('start_date', '>', now());
            })
            ->when($this->timeFilter === 'ongoing', function($query) {
                return $query->where('start_date', '<=', now())
                            ->where(function($q) {
                                $q->where('end_date', '>=', now())
                                  ->orWhereNull('end_date');
                            });
            })
            ->when($this->timeFilter === 'past', function($query) {
                return $query->where('end_date', '<', now())
                            ->orWhere(function($q) {
                                $q->whereNull('end_date')
                                  ->where('start_date', '<', now()->subHours(2)); // Assume 2-hour default duration
                            });
            })
            ->orderBy('start_date');

        $events = $eventsQuery->paginate(12);

        $myEvents = CommunityActivity::liveEvents()
            ->with(['creator', 'activeParticipants.user'])
            ->whereHas('participants', function($query) {
                $query->where('user_id', auth()->id())
                      ->whereIn('status', ['joined', 'completed']);
            })
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        $todayEvents = CommunityActivity::liveEvents()
            ->with(['creator', 'activeParticipants.user'])
            ->whereDate('start_date', today())
            ->orderBy('start_date')
            ->get();

        return view('livewire.community.partial.live-events', [
            'events' => $events,
            'myEvents' => $myEvents,
            'todayEvents' => $todayEvents,
        ]);
    }
}