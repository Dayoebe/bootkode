<?php

namespace App\Livewire\Career;

use Livewire\Component;
use App\Models\InterviewQuestionSet;
use App\Models\InterviewQuestion;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class AdminQuestionSets extends Component
{
    public $showCreateSetModal = false;
    public $editingSetId = null;
    public $name = '';
    public $description = '';
    public $type = 'technical';
    public $difficulty_level = 'intermediate';
    public $estimated_duration = 60;
    public $selectedQuestions = [];
    public $availableQuestions = [];
    
    public function mount()
    {
        $this->loadAvailableQuestions();
    }
    
    public function loadAvailableQuestions()
    {
        $this->availableQuestions = InterviewQuestion::where('is_active', true)
            ->where('is_approved', true)
            ->orderBy('type')
            ->orderBy('difficulty_level')
            ->get();
    }
    
    public function createSet()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required',
            'difficulty_level' => 'required',
            'estimated_duration' => 'required|integer|min:15',
        ]);
        
        $set = InterviewQuestionSet::create([
            'created_by' => Auth::id(),
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'difficulty_level' => $this->difficulty_level,
            'estimated_duration' => $this->estimated_duration,
            'is_active' => true,
        ]);
        
        // Add selected questions to set
        foreach ($this->selectedQuestions as $index => $questionId) {
            $set->addQuestion($questionId, $index + 1);
        }
        
        session()->flash('message', 'Question set created successfully!');
        $this->resetForm();
    }
    
    public function toggleQuestion($questionId)
    {
        if (in_array($questionId, $this->selectedQuestions)) {
            $this->selectedQuestions = array_diff($this->selectedQuestions, [$questionId]);
        } else {
            $this->selectedQuestions[] = $questionId;
        }
    }
    
    public function deleteSet($setId)
    {
        $set = InterviewQuestionSet::findOrFail($setId);
        $set->delete();
        
        session()->flash('message', 'Question set deleted successfully.');
    }
    
    private function resetForm()
    {
        $this->reset(['showCreateSetModal', 'name', 'description', 'type', 'difficulty_level', 'estimated_duration', 'selectedQuestions']);
    }
    
    public function render()
    {
        $questionSets = InterviewQuestionSet::with('creator', 'questions')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('livewire.career.admin-question-sets', [
            'questionSets' => $questionSets
        ]);
    }
}