<?php
// Partial Component 1: SubscriberManagement.php
namespace App\Livewire\Newsletter\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Admin\NewsletterSubscriber;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Writer;

class SubscriberManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $tagFilter = '';
    public $perPage = 15;
    public $selectedSubscribers = [];
    public $selectAll = false;

    // Modals
    public $showAddModal = false;
    public $showImportModal = false;
    public $showEditModal = false;

    // Form fields
    public $email = '';
    public $firstName = '';
    public $lastName = '';
    public $tags = '';
    public $status = 'active';
    public $editingId = null;

    // Import
    public $csvFile;
    public $importProgress = 0;
    public $importResults = null;

    protected $rules = [
        'email' => 'required|email|unique:newsletter_subscribers,email',
        'firstName' => 'nullable|string|max:255',
        'lastName' => 'nullable|string|max:255',
        'tags' => 'nullable|string',
        'status' => 'required|in:active,unsubscribed,bounced',
    ];

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedSubscribers = $this->subscribers->pluck('id')->toArray();
        } else {
            $this->selectedSubscribers = [];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function getSubscribersProperty()
    {
        $query = NewsletterSubscriber::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . $this->search . '%')
                  ->orWhere('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->tagFilter) {
            $query->whereJsonContains('tags', $this->tagFilter);
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function getAvailableTagsProperty()
    {
        return NewsletterSubscriber::whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values();
    }

    public function addSubscriber()
    {
        $this->validate();

        $tags = $this->tags ? explode(',', $this->tags) : [];
        $tags = array_map('trim', $tags);

        NewsletterSubscriber::create([
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'status' => $this->status,
            'tags' => $tags,
            'source' => 'manual',
        ]);

        $this->reset(['email', 'firstName', 'lastName', 'tags', 'status', 'showAddModal']);
        session()->flash('message', 'Subscriber added successfully!');
    }

    public function editSubscriber($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        
        $this->editingId = $id;
        $this->email = $subscriber->email;
        $this->firstName = $subscriber->first_name;
        $this->lastName = $subscriber->last_name;
        $this->tags = $subscriber->tags ? implode(', ', $subscriber->tags) : '';
        $this->status = $subscriber->status;
        $this->showEditModal = true;

        $this->rules['email'] = 'required|email|unique:newsletter_subscribers,email,' . $id;
    }

    public function updateSubscriber()
    {
        $this->validate();

        $subscriber = NewsletterSubscriber::findOrFail($this->editingId);
        $tags = $this->tags ? explode(',', $this->tags) : [];
        $tags = array_map('trim', $tags);

        $subscriber->update([
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'status' => $this->status,
            'tags' => $tags,
        ]);

        $this->reset(['email', 'firstName', 'lastName', 'tags', 'status', 'showEditModal', 'editingId']);
        session()->flash('message', 'Subscriber updated successfully!');
    }

    public function deleteSubscriber($id)
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        session()->flash('message', 'Subscriber deleted successfully!');
    }

    public function bulkAction($action)
    {
        if (empty($this->selectedSubscribers)) {
            session()->flash('error', 'No subscribers selected.');
            return;
        }

        $subscribers = NewsletterSubscriber::whereIn('id', $this->selectedSubscribers);

        switch ($action) {
            case 'activate':
                $subscribers->update(['status' => 'active']);
                session()->flash('message', 'Selected subscribers activated.');
                break;
            case 'unsubscribe':
                $subscribers->update([
                    'status' => 'unsubscribed',
                    'unsubscribed_at' => now()
                ]);
                session()->flash('message', 'Selected subscribers unsubscribed.');
                break;
            case 'delete':
                $subscribers->delete();
                session()->flash('message', 'Selected subscribers deleted.');
                break;
        }

        $this->selectedSubscribers = [];
        $this->selectAll = false;
    }

    public function importCsv()
    {
        $this->validate([
            'csvFile' => 'required|mimes:csv,txt|max:10240'
        ]);

        $path = $this->csvFile->store('temp');
        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setHeaderOffset(0);

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($csv as $offset => $record) {
            try {
                $email = trim($record['email'] ?? '');
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row " . ($offset + 2) . ": Invalid email format";
                    $skipped++;
                    continue;
                }

                NewsletterSubscriber::updateOrCreate(
                    ['email' => $email],
                    [
                        'first_name' => trim($record['first_name'] ?? ''),
                        'last_name' => trim($record['last_name'] ?? ''),
                        'status' => NewsletterSubscriber::STATUS_ACTIVE,
                        'source' => 'import',
                        'tags' => isset($record['tags']) ? explode(',', $record['tags']) : [],
                    ]
                );

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($offset + 2) . ": " . $e->getMessage();
                $skipped++;
            }
        }

        Storage::delete($path);

        $this->importResults = [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors
        ];

        $this->reset('csvFile');
        session()->flash('message', "Import completed: {$imported} imported, {$skipped} skipped.");
    }

    public function exportCsv()
    {
        $subscribers = NewsletterSubscriber::all();
        
        $csv = Writer::createFromString('');
        $csv->insertOne(['email', 'first_name', 'last_name', 'status', 'tags', 'subscribed_at']);

        foreach ($subscribers as $subscriber) {
            $csv->insertOne([
                $subscriber->email,
                $subscriber->first_name,
                $subscriber->last_name,
                $subscriber->status,
                $subscriber->tags ? implode(',', $subscriber->tags) : '',
                $subscriber->subscribed_at->format('Y-m-d H:i:s')
            ]);
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv->getContent();
        }, 'newsletter_subscribers_' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.newsletter.partials.subscriber-management', [
            'subscribers' => $this->subscribers,
            'availableTags' => $this->availableTags,
        ]);
    }
}
