<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class Toolbar extends Component
{
    public Course $course;
    public $sectionCount;
    public $lessonCount;
    public $categories;
  
    
    // Course pricing properties
    public $showPricingModal = false;
    public $price;
    public $is_free;
    public $is_premium;

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->updateCounts();
        $this->categories = Cache::remember('course_categories', 3600, fn() => CourseCategory::all());
        
        // Initialize pricing properties
        $this->price = $this->course->price;
        $this->is_free = $this->course->is_free;
        $this->is_premium = $this->course->is_premium;
    }    
    private function updateCounts()
    {
        // Force fresh data from database
        $this->course->refresh();
        $this->sectionCount = $this->course->sections()->count();
        $this->lessonCount = $this->course->sections()->withCount('lessons')->get()->sum('lessons_count');
    }

    #[On('outline-updated')]
    public function refreshCounts()
    {
        $this->updateCounts();
    }

    public function togglePublished()
    {
        try {
            DB::beginTransaction();

            // Get the current published status
            $currentStatus = $this->course->is_published;
            $newStatus = !$currentStatus;

            // Update only the is_published field to avoid triggering complex model events
            DB::table('courses')
                ->where('id', $this->course->id)
                ->update([
                    'is_published' => $newStatus,
                    'updated_at' => now()
                ]);

            // Update the local model instance
            $this->course->is_published = $newStatus;

            DB::commit();

            $statusText = $newStatus ? 'published' : 'unpublished';
            $this->notify("Course {$statusText} successfully!", 'success');

            // Dispatch event to parent component
            $this->dispatch('course-updated')->to('component.course-management.course-builder');

            Log::info("Course {$this->course->id} status changed to " . ($newStatus ? 'published' : 'unpublished'));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to toggle course publish status', [
                'course_id' => $this->course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->notify('Failed to update course status: Unable to save changes', 'error');

            // Ensure the local state matches the database
            $this->course->refresh();
        }
    }

    public function openPricingModal()
    {
        $this->showPricingModal = true;
        $this->price = $this->course->price;
        $this->is_free = $this->course->is_free;
        $this->is_premium = $this->course->is_premium;
    }

    public function closePricingModal()
    {
        $this->showPricingModal = false;
        $this->resetValidation();
    }

    public function updatePricing()
    {
        $this->validate([
            'price' => 'required_unless:is_free,true|numeric|min:0',
            'is_free' => 'boolean',
            'is_premium' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $this->course->update([
                'price' => $this->is_free ? 0 : $this->price,
                'is_free' => $this->is_free,
                'is_premium' => $this->is_premium,
                'updated_at' => now()
            ]);

            DB::commit();

            $this->notify('Course pricing updated successfully!', 'success');
            $this->closePricingModal();
            
            // Refresh course data
            $this->course->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update course pricing', [
                'course_id' => $this->course->id,
                'error' => $e->getMessage()
            ]);
            $this->notify('Failed to update pricing: ' . $e->getMessage(), 'error');
        }
    }

    public function notify($message, $type = 'success')
    {
        $this->dispatch('notify', message: $message, type: $type);
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.toolbar');
    }
}