<?php

namespace App\Livewire\Certification;

use Livewire\Component;
use App\Models\Course;
use App\Models\CertificateTemplate;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Request Certificate', 'description' => 'Request certificates for completed courses', 'icon' => 'fas fa-certificate', 'active' => 'certificates.request'])]

class CertificateRequest extends Component
{
    public $selectedCourse = null;
    public $availableCourses = [];
    public $availableTemplates = [];
    public $selectedTemplate = null;
    public $isRequesting = false;
    public $requestSuccess = false;
    public $certificateDetails = null;

    public function mount()
    {
        $this->availableCourses = auth()->user()->courses()
            ->whereDoesntHave('certificates', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('is_published', true)
            ->get();
    }

    public function updatedSelectedCourse($courseId)
    {
        if ($courseId) {
            $this->availableTemplates = CertificateTemplate::where('is_active', true)->get();
            $this->selectedTemplate = null;
        } else {
            $this->availableTemplates = [];
            $this->selectedTemplate = null;
        }
    }

    public function requestCertificate()
    {
        $this->validate([
            'selectedCourse' => 'required|exists:courses,id',
            'selectedTemplate' => 'required|exists:certificate_templates,id',
        ]);

        $course = Course::findOrFail($this->selectedCourse);

        if (!auth()->user()->hasCompletedCourse($course)) {
            $this->addError('selectedCourse', 'You must complete all lessons in the course before requesting a certificate.');
            return;
        }

        $this->isRequesting = true;

        try {
            DB::transaction(function () {
                $certificate = Certificate::create([
                    'user_id' => auth()->id(),
                    'course_id' => $this->selectedCourse,
                    'template_id' => $this->selectedTemplate,
                    'uuid' => Str::uuid(),
                    'verification_code' => Str::random(16),
                    'issue_date' => now(),
                    'status' => auth()->user()->hasRole('super_admin') ? 'approved' : 'pending',
                    'approved_by' => auth()->user()->hasRole('super_admin') ? auth()->id() : null,
                    'approved_at' => auth()->user()->hasRole('super_admin') ? now() : null,
                ]);

                $this->certificateDetails = $certificate->load(['course', 'template']);
                $this->requestSuccess = true;
            });
        } catch (\Exception $e) {
            $this->addError('certificate_request', 'Failed to request certificate. Please try again.');
        } finally {
            $this->isRequesting = false;
        }
    }

    public function render()
    {
        return view('livewire.certification.certificate-request');
    }
}