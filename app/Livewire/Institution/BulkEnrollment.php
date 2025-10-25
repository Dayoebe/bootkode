<?php

namespace App\Livewire\Institution;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Core\Institution;
use App\Models\Learning\Course;
use App\Models\Admin\BulkEnrollmentBatch;
use App\Services\InstitutionService;
use Illuminate\Support\Facades\Storage;

class BulkEnrollment extends Component
{
    use WithPagination, WithFileUploads;

    // Upload form
    public $showUploadModal = false;
    public $csvFile;
    public $selectedInstitution = '';
    public $selectedCourses = [];
    public $batchName = '';
    public $batchDescription = '';

    // Filters
    public $statusFilter = 'all';
    public $institutionFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // View states
    public $showDetailsModal = false;
    public $selectedBatch = null;

    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,txt|max:10240',
        'selectedInstitution' => 'required|exists:institutions,id',
        'selectedCourses' => 'required|array|min:1',
        'selectedCourses.*' => 'exists:courses,id',
        'batchName' => 'required|string|max:255',
        'batchDescription' => 'nullable|string|max:1000'
    ];

    public function mount()
    {
        $this->batchName = 'Bulk Enrollment ' . now()->format('M d, Y');
    }

    public function openUploadModal()
    {
        $this->resetForm();
        $this->showUploadModal = true;
    }

    public function closeModals()
    {
        $this->showUploadModal = false;
        $this->showDetailsModal = false;
        $this->selectedBatch = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function uploadCsv()
    {
        $this->validate();

        try {
            $institution = Institution::findOrFail($this->selectedInstitution);
            
            // Store the CSV file
            $filePath = $this->csvFile->store('bulk-enrollments', 'local');
            
            // Process bulk enrollment
            $batch = app(InstitutionService::class)->processBulkEnrollment(
                $institution,
                storage_path('app/' . $filePath),
                $this->selectedCourses,
                [
                    'name' => $this->batchName,
                    'description' => $this->batchDescription,
                    'original_filename' => $this->csvFile->getClientOriginalName()
                ]
            );

            session()->flash('message', 'Bulk enrollment started successfully! Processing will continue in the background.');
            $this->closeModals();

        } catch (\Exception $e) {
            \Log::error('Bulk enrollment upload failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to start bulk enrollment: ' . $e->getMessage());
        }
    }

    public function viewDetails($batchId)
    {
        $this->selectedBatch = BulkEnrollmentBatch::with(['institution', 'creator'])
            ->findOrFail($batchId);
        $this->showDetailsModal = true;
    }

    public function retryBatch($batchId)
    {
        try {
            $batch = BulkEnrollmentBatch::findOrFail($batchId);
            
            if ($batch->isFailed() || $batch->status === 'cancelled') {
                $batch->update([
                    'status' => 'pending',
                    'started_at' => null,
                    'completed_at' => null,
                    'processed_records' => 0,
                    'successful_enrollments' => 0,
                    'failed_enrollments' => 0,
                    'errors' => []
                ]);

                \App\Jobs\ProcessBulkEnrollment::dispatch($batch);
                
                session()->flash('message', 'Batch retry initiated successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to retry batch: ' . $e->getMessage());
        }
    }

    public function cancelBatch($batchId)
    {
        try {
            $batch = BulkEnrollmentBatch::findOrFail($batchId);
            
            if ($batch->isPending() || $batch->isProcessing()) {
                $batch->update(['status' => 'cancelled']);
                session()->flash('message', 'Batch cancelled successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel batch: ' . $e->getMessage());
        }
    }

    public function deleteBatch($batchId)
    {
        try {
            $batch = BulkEnrollmentBatch::findOrFail($batchId);
            
            // Delete file if exists
            if ($batch->file_path && Storage::disk('local')->exists($batch->file_path)) {
                Storage::disk('local')->delete($batch->file_path);
            }
            
            $batch->delete();
            session()->flash('message', 'Batch deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete batch: ' . $e->getMessage());
        }
    }

    public function downloadErrorReport($batchId)
    {
        $batch = BulkEnrollmentBatch::findOrFail($batchId);
        
        if (empty($batch->errors)) {
            session()->flash('warning', 'No errors to download for this batch.');
            return;
        }

        return response()->streamDownload(function () use ($batch) {
            $csv = "Row,Email,Error\n";
            
            foreach ($batch->errors as $error) {
                $csv .= sprintf(
                    "%s,%s,%s\n",
                    $error['row'] ?? 'N/A',
                    $error['email'] ?? 'Unknown',
                    str_replace('"', '""', $error['message'] ?? 'Unknown error')
                );
            }
            
            echo $csv;
        }, 'bulk-enrollment-errors-' . $batch->id . '.csv');
    }

    public function downloadSampleCsv()
    {
        return response()->streamDownload(function () {
            $csv = "name,email,department,employee_id,institution_role\n";
            $csv .= "John Doe,john.doe@example.com,IT,EMP001,student\n";
            $csv .= "Jane Smith,jane.smith@example.com,HR,EMP002,instructor\n";
            $csv .= "Bob Wilson,bob.wilson@example.com,Finance,EMP003,student\n";
            
            echo $csv;
        }, 'bulk-enrollment-sample.csv');
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedInstitutionFilter()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->csvFile = null;
        $this->selectedInstitution = '';
        $this->selectedCourses = [];
        $this->batchName = 'Bulk Enrollment ' . now()->format('M d, Y');
        $this->batchDescription = '';
    }

    private function getBatchesQuery()
    {
        $query = BulkEnrollmentBatch::with(['institution', 'creator']);

        // Apply filters
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->institutionFilter !== 'all') {
            $query->where('institution_id', $this->institutionFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    public function render()
    {
        $batches = $this->getBatchesQuery()->paginate($this->perPage);
        
        return view('livewire.institution.bulk-enrollment', [
            'batches' => $batches,
            'institutions' => Institution::where('status', 'active')->get(),
            'courses' => Course::where('is_published', true)->get(),
            'statuses' => BulkEnrollmentBatch::STATUSES
        ]);
    }
}