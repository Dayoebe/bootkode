<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Question;
use App\Models\Assessment;
use Livewire\Component;

class QnaCriteriaManager extends Component
{
    public $assessmentId;
    public $assessment;
    public $topics = [];
    public $showCreateForm = false;
    public $editingTopic = null;
    
    // Topic form fields
    public $topicTitle = '';
    public $topicDescription = '';
    public $topicType = 'discussion';
    public $points = 5;
    public $isRequired = true;
    public $timeLimit = null;
    
    // Q&A specific fields
    public $minResponses = 1;
    public $minResponseLength = 50;
    public $allowPeerReview = true;
    public $moderatorApproval = false;
    public $discussionPrompts = [];
    public $evaluationCriteria = [];
    
    protected $listeners = [
        'topicCreated' => 'loadTopics',
        'topicUpdated' => 'loadTopics',
        'topicDeleted' => 'loadTopics',
    ];

    protected function rules()
    {
        return [
            'topicTitle' => 'required|string|max:255',
            'topicDescription' => 'required|string|max:1000',
            'topicType' => 'required|in:discussion,debate,peer_review,reflection,case_study',
            'points' => 'required|numeric|min:1|max:100',
            'isRequired' => 'boolean',
            'timeLimit' => 'nullable|integer|min:1|max:10080', // up to 1 week in minutes
            'minResponses' => 'required|integer|min:1|max:10',
            'minResponseLength' => 'required|integer|min:10|max:1000',
            'allowPeerReview' => 'boolean',
            'moderatorApproval' => 'boolean',
        ];
    }

    public function mount($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $this->assessment = Assessment::findOrFail($assessmentId);
        $this->loadTopics();
        
        // Initialize default discussion prompts
        $this->discussionPrompts = [
            'What are your initial thoughts on this topic?',
            'How does this relate to your experience?',
            'What questions do you have?'
        ];

        // Initialize default evaluation criteria
        $this->evaluationCriteria = [
            ['name' => 'Participation Quality', 'weight' => 40, 'description' => 'Depth and relevance of contributions'],
            ['name' => 'Critical Thinking', 'weight' => 30, 'description' => 'Analysis and evaluation of ideas'],
            ['name' => 'Peer Engagement', 'weight' => 20, 'description' => 'Meaningful interaction with others'],
            ['name' => 'Knowledge Application', 'weight' => 10, 'description' => 'Use of course concepts'],
        ];
    }

    public function loadTopics()
    {
        $this->topics = Question::where('assessment_id', $this->assessmentId)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        if (!$this->showCreateForm) {
            $this->resetForm();
        }
    }

    public function selectTopicType($type)
    {
        $this->topicType = $type;
        $this->adjustDefaultsForType();
    }

    protected function adjustDefaultsForType()
    {
        switch ($this->topicType) {
            case 'discussion':
                $this->minResponses = 2;
                $this->minResponseLength = 100;
                $this->allowPeerReview = true;
                break;
            case 'debate':
                $this->minResponses = 3;
                $this->minResponseLength = 150;
                $this->allowPeerReview = true;
                $this->discussionPrompts = [
                    'Present your position on this topic',
                    'Provide evidence to support your argument',
                    'Address counterarguments'
                ];
                break;
            case 'peer_review':
                $this->minResponses = 1;
                $this->minResponseLength = 200;
                $this->allowPeerReview = false;
                $this->discussionPrompts = [
                    'What are the strengths of this work?',
                    'What areas could be improved?',
                    'What suggestions do you have?'
                ];
                break;
            case 'reflection':
                $this->minResponses = 1;
                $this->minResponseLength = 150;
                $this->allowPeerReview = false;
                break;
            case 'case_study':
                $this->minResponses = 2;
                $this->minResponseLength = 250;
                $this->allowPeerReview = true;
                $this->discussionPrompts = [
                    'Analyze the key issues in this case',
                    'What solutions would you propose?',
                    'How would you implement your recommendations?'
                ];
                break;
        }
    }

    public function addDiscussionPrompt()
    {
        $this->discussionPrompts[] = '';
    }

    public function removeDiscussionPrompt($index)
    {
        if (count($this->discussionPrompts) > 1) {
            array_splice($this->discussionPrompts, $index, 1);
        }
    }

    public function addEvaluationCriteria()
    {
        $this->evaluationCriteria[] = [
            'name' => '',
            'weight' => 10,
            'description' => ''
        ];
    }

    public function removeEvaluationCriteria($index)
    {
        if (count($this->evaluationCriteria) > 1) {
            array_splice($this->evaluationCriteria, $index, 1);
        }
    }

    public function createTopic()
    {
        $this->validate();

        $topicData = [
            'assessment_id' => $this->assessmentId,
            'question_text' => $this->topicTitle,
            'question_type' => 'qna_topic',
            'points' => $this->points,
            'explanation' => $this->topicDescription,
            'is_required' => $this->isRequired,
            'time_limit' => $this->timeLimit,
            'order' => count($this->topics) + 1,
        ];

        // Store Q&A-specific data
        $qnaData = [
            'topic_type' => $this->topicType,
            'min_responses' => $this->minResponses,
            'min_response_length' => $this->minResponseLength,
            'allow_peer_review' => $this->allowPeerReview,
            'moderator_approval' => $this->moderatorApproval,
            'discussion_prompts' => array_filter($this->discussionPrompts),
            'evaluation_criteria' => $this->evaluationCriteria,
        ];

        $topicData['options'] = json_encode($qnaData);

        Question::create($topicData);

        $this->loadTopics();
        $this->resetForm();
        $this->showCreateForm = false;
        
        session()->flash('success', 'Discussion topic created successfully!');
    }

    public function editTopic($topicId)
    {
        $topic = collect($this->topics)->firstWhere('id', $topicId);
        if ($topic) {
            $this->editingTopic = $topic;
            $this->fillFormFromTopic($topic);
            $this->showCreateForm = true;
        }
    }

    protected function fillFormFromTopic($topic)
    {
        $this->topicTitle = $topic['question_text'];
        $this->topicDescription = $topic['explanation'] ?? '';
        $this->points = $topic['points'];
        $this->isRequired = $topic['is_required'] ?? true;
        $this->timeLimit = $topic['time_limit'];

        $qnaData = json_decode($topic['options'], true) ?? [];
        $this->topicType = $qnaData['topic_type'] ?? 'discussion';
        $this->minResponses = $qnaData['min_responses'] ?? 1;
        $this->minResponseLength = $qnaData['min_response_length'] ?? 50;
        $this->allowPeerReview = $qnaData['allow_peer_review'] ?? true;
        $this->moderatorApproval = $qnaData['moderator_approval'] ?? false;
        $this->discussionPrompts = $qnaData['discussion_prompts'] ?? $this->discussionPrompts;
        $this->evaluationCriteria = $qnaData['evaluation_criteria'] ?? $this->evaluationCriteria;
    }

    public function updateTopic()
    {
        $this->validate();

        if ($this->editingTopic) {
            $topic = Question::findOrFail($this->editingTopic['id']);
            
            $qnaData = [
                'topic_type' => $this->topicType,
                'min_responses' => $this->minResponses,
                'min_response_length' => $this->minResponseLength,
                'allow_peer_review' => $this->allowPeerReview,
                'moderator_approval' => $this->moderatorApproval,
                'discussion_prompts' => array_filter($this->discussionPrompts),
                'evaluation_criteria' => $this->evaluationCriteria,
            ];

            $topic->update([
                'question_text' => $this->topicTitle,
                'explanation' => $this->topicDescription,
                'points' => $this->points,
                'is_required' => $this->isRequired,
                'time_limit' => $this->timeLimit,
                'options' => json_encode($qnaData),
            ]);

            $this->loadTopics();
            $this->resetForm();
            $this->showCreateForm = false;
            
            session()->flash('success', 'Discussion topic updated successfully!');
        }
    }

    public function deleteTopic($topicId)
    {
        Question::findOrFail($topicId)->delete();
        $this->loadTopics();
        
        session()->flash('success', 'Discussion topic deleted successfully!');
    }

    public function duplicateTopic($topicId)
    {
        $originalTopic = Question::findOrFail($topicId);
        $duplicatedTopic = $originalTopic->replicate();
        $duplicatedTopic->question_text = $originalTopic->question_text . ' (Copy)';
        $duplicatedTopic->order = count($this->topics) + 1;
        $duplicatedTopic->save();

        $this->loadTopics();
        session()->flash('success', 'Discussion topic duplicated successfully!');
    }

    public function reorderTopics($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Question::where('id', $id)->update(['order' => $index + 1]);
        }
        
        $this->loadTopics();
    }

    protected function resetForm()
    {
        $this->topicTitle = '';
        $this->topicDescription = '';
        $this->topicType = 'discussion';
        $this->points = 5;
        $this->isRequired = true;
        $this->timeLimit = null;
        $this->minResponses = 1;
        $this->minResponseLength = 50;
        $this->allowPeerReview = true;
        $this->moderatorApproval = false;
        $this->discussionPrompts = [
            'What are your initial thoughts on this topic?',
            'How does this relate to your experience?',
            'What questions do you have?'
        ];
        $this->evaluationCriteria = [
            ['name' => 'Participation Quality', 'weight' => 40, 'description' => 'Depth and relevance of contributions'],
            ['name' => 'Critical Thinking', 'weight' => 30, 'description' => 'Analysis and evaluation of ideas'],
            ['name' => 'Peer Engagement', 'weight' => 20, 'description' => 'Meaningful interaction with others'],
            ['name' => 'Knowledge Application', 'weight' => 10, 'description' => 'Use of course concepts'],
        ];
        $this->editingTopic = null;
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.qna-criteria-manager');
    }
}