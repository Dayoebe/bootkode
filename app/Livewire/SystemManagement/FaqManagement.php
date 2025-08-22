<?php

namespace App\Livewire\SystemManagement;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'FAQ Management', 'description' => 'Manage FAQs for Help & Support', 'icon' => 'fas fa-question', 'active' => 'faq.management'])]
class FaqManagement extends Component
{
    use WithPagination;

    public $question = '';
    public $answer = '';
    public $order = 0;
    public $editId = null;

    protected $rules = [
        'question' => ['required', 'string', 'max:255'],
        'answer' => ['required', 'string'],
        'order' => ['integer', 'min:0'],
    ];

    public function saveFaq()
    {
        $this->validate();

        if ($this->editId) {
            $faq = Faq::findOrFail($this->editId);
            $faq->update([
                'question' => $this->question,
                'answer' => $this->answer,
                'order' => $this->order,
            ]);
            $this->dispatch('notify', 'FAQ updated successfully!', 'success');
        } else {
            Faq::create([
                'question' => $this->question,
                'answer' => $this->answer,
                'order' => $this->order,
            ]);
            $this->dispatch('notify', 'FAQ created successfully!', 'success');
        }

        $this->reset(['question', 'answer', 'order', 'editId']);
    }

    public function editFaq($faqId)
    {
        $faq = Faq::findOrFail($faqId);
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->order = $faq->order;
        $this->editId = $faq->id;
    }

    public function deleteFaq($faqId)
    {
        Faq::findOrFail($faqId)->delete();
        $this->dispatch('notify', 'FAQ deleted successfully!', 'success');
    }

    public function render()
    {
        $faqs = Faq::orderBy('order')->paginate(10);

        return view('livewire.system-management.faq-management', [
            'faqs' => $faqs,
        ]);
    }
}