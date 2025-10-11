<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use App\Models\MentorProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Mentor Resources', 
    'description' => 'Resources and Tools for Mentors', 
    'icon' => 'fas fa-tools', 
    'active' => 'mentorship'
])]
class MentorResources extends Component
{
    public $activeCategory = 'getting-started';
    
    public $resources = [
        'getting-started' => [
            'title' => 'Getting Started',
            'icon' => 'fas fa-rocket',
            'items' => [
                [
                    'title' => 'Mentor Onboarding Guide',
                    'description' => 'Everything you need to know to start mentoring effectively',
                    'type' => 'document',
                    'url' => '#',
                ],
                [
                    'title' => 'First Session Checklist',
                    'description' => 'Prepare for your first mentoring session',
                    'type' => 'checklist',
                    'url' => '#',
                ],
                [
                    'title' => 'Setting Expectations',
                    'description' => 'How to set clear goals and boundaries with mentees',
                    'type' => 'guide',
                    'url' => '#',
                ],
            ]
        ],
        'best-practices' => [
            'title' => 'Best Practices',
            'icon' => 'fas fa-star',
            'items' => [
                [
                    'title' => 'Effective Communication Strategies',
                    'description' => 'Master the art of clear and supportive communication',
                    'type' => 'guide',
                    'url' => '#',
                ],
                [
                    'title' => 'Providing Constructive Feedback',
                    'description' => 'Techniques for giving feedback that motivates growth',
                    'type' => 'guide',
                    'url' => '#',
                ],
                [
                    'title' => 'Time Management for Mentors',
                    'description' => 'Balance mentoring with your other commitments',
                    'type' => 'guide',
                    'url' => '#',
                ],
                [
                    'title' => 'Building Trust and Rapport',
                    'description' => 'Create strong mentor-mentee relationships',
                    'type' => 'guide',
                    'url' => '#',
                ],
            ]
        ],
        'technical-resources' => [
            'title' => 'Technical Resources',
            'icon' => 'fas fa-code',
            'items' => [
                [
                    'title' => 'Code Review Best Practices',
                    'description' => 'Guidelines for effective code reviews',
                    'type' => 'document',
                    'url' => '#',
                ],
                [
                    'title' => 'Project Assessment Templates',
                    'description' => 'Templates for evaluating mentee projects',
                    'type' => 'template',
                    'url' => '#',
                ],
                [
                    'title' => 'Learning Path Recommendations',
                    'description' => 'Suggested learning paths for different tech stacks',
                    'type' => 'guide',
                    'url' => '#',
                ],
                [
                    'title' => 'Technical Interview Prep',
                    'description' => 'Resources for helping mentees prepare for interviews',
                    'type' => 'guide',
                    'url' => '#',
                ],
            ]
        ],
        'templates' => [
            'title' => 'Templates & Tools',
            'icon' => 'fas fa-clipboard-list',
            'items' => [
                [
                    'title' => 'Session Notes Template',
                    'description' => 'Structured template for documenting sessions',
                    'type' => 'template',
                    'url' => '#',
                ],
                [
                    'title' => 'Goal Setting Worksheet',
                    'description' => 'Help mentees define and track their goals',
                    'type' => 'template',
                    'url' => '#',
                ],
                [
                    'title' => 'Progress Tracking Sheet',
                    'description' => 'Monitor mentee progress over time',
                    'type' => 'template',
                    'url' => '#',
                ],
                [
                    'title' => 'Mentorship Agreement Template',
                    'description' => 'Formalize expectations and commitments',
                    'type' => 'template',
                    'url' => '#',
                ],
            ]
        ],
        'career-guidance' => [
            'title' => 'Career Guidance',
            'icon' => 'fas fa-briefcase',
            'items' => [
                [
                    'title' => 'Resume Review Guidelines',
                    'description' => 'How to help mentees improve their resumes',
                    'type' => 'guide',
                    'url' => '#',
                ],
                [
                    'title' => 'LinkedIn Profile Optimization',
                    'description' => 'Best practices for professional networking',
                    'type' => 'guide',
                    'url' => '#',
                ],
                [
                    'title' => 'Salary Negotiation Tips',
                    'description' => 'Guide mentees through compensation discussions',
                    'type' => 'guide',
                    'url' => '#',
                ],
                [
                    'title' => 'Career Transition Strategies',
                    'description' => 'Help mentees navigate career changes',
                    'type' => 'guide',
                    'url' => '#',
                ],
            ]
        ],
        'community' => [
            'title' => 'Community & Support',
            'icon' => 'fas fa-users',
            'items' => [
                [
                    'title' => 'Mentor Community Forum',
                    'description' => 'Connect with other mentors and share experiences',
                    'type' => 'link',
                    'url' => '#',
                ],
                [
                    'title' => 'Monthly Mentor Meetups',
                    'description' => 'Join virtual meetups with fellow mentors',
                    'type' => 'event',
                    'url' => '#',
                ],
                [
                    'title' => 'Mentor Support Channel',
                    'description' => 'Get help when you need it',
                    'type' => 'link',
                    'url' => '#',
                ],
                [
                    'title' => 'Success Stories',
                    'description' => 'Read inspiring mentorship success stories',
                    'type' => 'blog',
                    'url' => '#',
                ],
            ]
        ],
    ];

    public function mount()
    {
        $this->checkMentorAccess();
    }

    private function checkMentorAccess()
    {
        $user = Auth::user();
        if (!$user->isMentor() && !$user->isAcademyAdmin() && !$user->isSuperAdmin()) {
            session()->flash('error', 'You need to be a mentor to access this page.');
            return redirect()->route('mentorship.hub');
        }
    }

    public function setActiveCategory($category)
    {
        $this->activeCategory = $category;
    }

    public function render()
    {
        return view('livewire.mentorship.mentor-resources');
    }
}