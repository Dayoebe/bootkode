<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ResumeProfile;
use Livewire\Attributes\Layout;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

#[Layout('layouts.dashboard', ['title' => 'Resume Builder', 'description' => 'Create your professional resume', 'icon' => 'fas fa-file-alt', 'active' => 'resume.builder'])]

class ResumeBuilder extends Component
{
    use WithFileUploads;

    // Main properties
    public $resume;
    public $activeSection = 'personal';
    public $viewMode = 'edit'; // edit, preview, settings
    public $isLoading = false;
    public $aiProcessing = false;

    // Form data - all sections
    public $personalForm = [];
    public $experienceForm = [];
    public $educationForm = [];
    public $skillForm = [];
    public $projectForm = [];
    public $certificationForm = [];
    public $languageForm = [];
    public $referenceForm = [];
    public $volunteerForm = [];
    public $publicationForm = [];
    public $awardForm = [];
    public $customSectionForm = [];

    // Editing states
    public $editingExperienceIndex = null;
    public $editingEducationIndex = null;
    public $editingSkillIndex = null;
    public $editingProjectIndex = null;
    public $editingCertificationIndex = null;
    public $editingLanguageIndex = null;
    public $editingReferenceIndex = null;
    public $editingVolunteerIndex = null;
    public $editingPublicationIndex = null;
    public $editingAwardIndex = null;
    public $editingCustomSectionIndex = null;

    // Template & styling
    public $selectedTemplate = 'modern';
    public $selectedColorScheme = 'professional';
    public $selectedFont = 'inter';
    
    // Section visibility
    public $sectionVisibility = [];
    public $sectionOrder = [];

    // File uploads
    public $profileImageUpload;
    public $documentUpload;

    // AI features
    public $aiSuggestions = [];
    public $aiEnhancedText = '';

    // Available sections with international relevance
    public $sections = [
        'personal' => ['icon' => 'fas fa-user', 'label' => 'Personal Info', 'required' => true, 'order' => 1],
        'summary' => ['icon' => 'fas fa-align-left', 'label' => 'Professional Summary', 'required' => true, 'order' => 2],
        'experience' => ['icon' => 'fas fa-briefcase', 'label' => 'Work Experience', 'required' => true, 'order' => 3],
        'education' => ['icon' => 'fas fa-graduation-cap', 'label' => 'Education', 'required' => true, 'order' => 4],
        'skills' => ['icon' => 'fas fa-cogs', 'label' => 'Skills', 'required' => true, 'order' => 5],
        'projects' => ['icon' => 'fas fa-project-diagram', 'label' => 'Projects', 'required' => false, 'order' => 6],
        'certifications' => ['icon' => 'fas fa-certificate', 'label' => 'Certifications', 'required' => false, 'order' => 7],
        'languages' => ['icon' => 'fas fa-language', 'label' => 'Languages', 'required' => false, 'order' => 8],
        'volunteer' => ['icon' => 'fas fa-heart', 'label' => 'Volunteer Work', 'required' => false, 'order' => 9],
        'publications' => ['icon' => 'fas fa-book', 'label' => 'Publications', 'required' => false, 'order' => 10],
        'awards' => ['icon' => 'fas fa-trophy', 'label' => 'Awards & Honors', 'required' => false, 'order' => 11],
        'references' => ['icon' => 'fas fa-users', 'label' => 'References', 'required' => false, 'order' => 12],
    ];

    // Premium templates
    public $templates = [
        'modern' => [
            'name' => 'Modern Professional',
            'description' => 'Clean, minimalist design with sidebar',
            'preview' => 'modern-preview.jpg',
            'is_premium' => false,
            'features' => ['Clean layout', 'Skills visualization', 'Professional colors']
        ],
        'classic' => [
            'name' => 'Classic Traditional',
            'description' => 'Traditional format with centered header',
            'preview' => 'classic-preview.jpg', 
            'is_premium' => false,
            'features' => ['Traditional layout', 'Conservative design', 'ATS-friendly']
        ],
        'creative' => [
            'name' => 'Creative Design',
            'description' => 'Bold colors and creative layout',
            'preview' => 'creative-preview.jpg',
            'is_premium' => true,
            'features' => ['Creative design', 'Bold typography', 'Visual elements']
        ],
        'executive' => [
            'name' => 'Executive Premium',
            'description' => 'Sophisticated design for senior professionals',
            'preview' => 'executive-preview.jpg',
            'is_premium' => true,
            'features' => ['Executive style', 'Premium layout', 'Leadership focus']
        ],
        'minimal' => [
            'name' => 'Minimal Clean',
            'description' => 'Ultra-clean design with perfect spacing',
            'preview' => 'minimal-preview.jpg',
            'is_premium' => true,
            'features' => ['Minimal design', 'Perfect spacing', 'Typography focus']
        ],
        'tech' => [
            'name' => 'Tech Professional',
            'description' => 'Modern design for tech professionals',
            'preview' => 'tech-preview.jpg',
            'is_premium' => true,
            'features' => ['Tech-focused', 'Code snippets', 'GitHub integration']
        ],
        'academic' => [
            'name' => 'Academic Scholar',
            'description' => 'Perfect for academic and research positions',
            'preview' => 'academic-preview.jpg',
            'is_premium' => true,
            'features' => ['Academic layout', 'Publications focus', 'Research emphasis']
        ]
    ];

    // Color schemes
    public $colorSchemes = [
        'professional' => ['name' => 'Professional Blue', 'primary' => '#2563eb', 'secondary' => '#64748b'],
        'elegant' => ['name' => 'Elegant Navy', 'primary' => '#1e293b', 'secondary' => '#475569'],
        'modern' => ['name' => 'Modern Purple', 'primary' => '#7c3aed', 'secondary' => '#6b7280'],
        'creative' => ['name' => 'Creative Orange', 'primary' => '#ea580c', 'secondary' => '#78716c'],
        'minimal' => ['name' => 'Minimal Gray', 'primary' => '#374151', 'secondary' => '#9ca3af'],
        'success' => ['name' => 'Success Green', 'primary' => '#059669', 'secondary' => '#6b7280'],
        'warm' => ['name' => 'Warm Red', 'primary' => '#dc2626', 'secondary' => '#78716c'],
        'tech' => ['name' => 'Tech Cyan', 'primary' => '#0891b2', 'secondary' => '#64748b'],
    ];

    // Font options
    public $fonts = [
        'inter' => 'Inter (Modern Sans-serif)',
        'roboto' => 'Roboto (Clean & Professional)',
        'open-sans' => 'Open Sans (Friendly & Readable)',
        'lato' => 'Lato (Corporate Style)',
        'merriweather' => 'Merriweather (Elegant Serif)',
        'source-sans' => 'Source Sans Pro (Technical)',
        'poppins' => 'Poppins (Contemporary)',
        'montserrat' => 'Montserrat (Modern Geometric)',
    ];

    // Skill categories for international relevance
    public $skillCategories = [
        'Technical' => 'Technical Skills',
        'Programming' => 'Programming Languages',
        'Software' => 'Software & Tools',
        'Design' => 'Design & Creative',
        'Management' => 'Management & Leadership',
        'Communication' => 'Communication',
        'Languages' => 'Language Skills',
        'Digital' => 'Digital Marketing',
        'Finance' => 'Finance & Accounting',
        'Healthcare' => 'Healthcare & Medical',
        'Education' => 'Education & Training',
        'Sales' => 'Sales & Business Development',
        'Operations' => 'Operations & Logistics',
        'Research' => 'Research & Analytics',
        'Legal' => 'Legal & Compliance',
        'Other' => 'Other Skills'
    ];

    protected $rules = [
        // Personal Info
        'personalForm.full_name' => 'required|string|max:255',
        'personalForm.professional_title' => 'required|string|max:255',
        'personalForm.email' => 'required|email|max:255',
        'personalForm.phone' => 'nullable|string|max:20',
        'personalForm.location' => 'nullable|string|max:255',
        'personalForm.website' => 'nullable|url|max:255',
        'personalForm.linkedin' => 'nullable|url|max:255',
        'personalForm.github' => 'nullable|url|max:255',
        'personalForm.professional_summary' => 'required|string|max:1000',
        
        // Experience
        'experienceForm.position' => 'required|string|max:255',
        'experienceForm.company' => 'required|string|max:255',
        'experienceForm.location' => 'nullable|string|max:255',
        'experienceForm.start_date' => 'required|date',
        'experienceForm.end_date' => 'nullable|date|after_or_equal:experienceForm.start_date',
        'experienceForm.current' => 'nullable|boolean',
        'experienceForm.description' => 'nullable|string',
        
        // Education
        'educationForm.degree' => 'required|string|max:255',
        'educationForm.field_of_study' => 'nullable|string|max:255',
        'educationForm.institution' => 'required|string|max:255',
        'educationForm.location' => 'nullable|string|max:255',
        'educationForm.start_date' => 'required|date',
        'educationForm.end_date' => 'nullable|date|after_or_equal:educationForm.start_date',
        'educationForm.gpa' => 'nullable|numeric|min:0|max:4',
        'educationForm.description' => 'nullable|string',
        
        // Skills
        'skillForm.name' => 'required|string|max:255',
        'skillForm.category' => 'nullable|string|max:255',
        'skillForm.proficiency' => 'required|integer|min:1|max:100',
        
        // Projects
        'projectForm.name' => 'required|string|max:255',
        'projectForm.description' => 'nullable|string',
        'projectForm.technologies' => 'nullable|string',
        'projectForm.url' => 'nullable|url|max:255',
        'projectForm.github_url' => 'nullable|url|max:255',
        'projectForm.start_date' => 'nullable|date',
        'projectForm.end_date' => 'nullable|date|after_or_equal:projectForm.start_date',
        
        // Certifications
        'certificationForm.name' => 'required|string|max:255',
        'certificationForm.issuer' => 'required|string|max:255',
        'certificationForm.issue_date' => 'nullable|date',
        'certificationForm.expiry_date' => 'nullable|date|after_or_equal:certificationForm.issue_date',
        'certificationForm.credential_id' => 'nullable|string|max:255',
        'certificationForm.credential_url' => 'nullable|url|max:255',
        
        // Languages
        'languageForm.language' => 'required|string|max:255',
        'languageForm.proficiency' => 'required|string|max:255',
        'languageForm.certification' => 'nullable|string|max:255',
        
        // Volunteer Work
        'volunteerForm.organization' => 'required|string|max:255',
        'volunteerForm.position' => 'required|string|max:255',
        'volunteerForm.start_date' => 'required|date',
        'volunteerForm.end_date' => 'nullable|date|after_or_equal:volunteerForm.start_date',
        'volunteerForm.current' => 'nullable|boolean',
        'volunteerForm.description' => 'nullable|string',
        
        // Publications
        'publicationForm.title' => 'required|string|max:255',
        'publicationForm.authors' => 'nullable|string|max:500',
        'publicationForm.publication' => 'nullable|string|max:255',
        'publicationForm.publish_date' => 'nullable|date',
        'publicationForm.url' => 'nullable|url|max:255',
        'publicationForm.doi' => 'nullable|string|max:255',
        
        // Awards
        'awardForm.title' => 'required|string|max:255',
        'awardForm.issuer' => 'required|string|max:255',
        'awardForm.date' => 'nullable|date',
        'awardForm.description' => 'nullable|string',
        
        // References
        'referenceForm.name' => 'required|string|max:255',
        'referenceForm.position' => 'nullable|string|max:255',
        'referenceForm.company' => 'nullable|string|max:255',
        'referenceForm.email' => 'nullable|email|max:255',
        'referenceForm.phone' => 'nullable|string|max:20',
        'referenceForm.relationship' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->loadOrCreateResume();
        $this->initializeForms();
        $this->initializeSectionSettings();
    }

    protected function loadOrCreateResume()
    {
        $user = auth()->user();
        $this->resume = $user->resumeProfile ?? ResumeProfile::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'location' => $user->getFullAddressAttribute(),
            'professional_summary' => $user->bio,
        ]);
    }

    protected function initializeForms()
    {
        // Initialize all form arrays
        $this->personalForm = [
            'full_name' => $this->resume->full_name,
            'professional_title' => $this->resume->professional_title,
            'email' => $this->resume->email,
            'phone' => $this->resume->phone,
            'location' => $this->resume->location,
            'website' => $this->resume->website,
            'linkedin' => $this->resume->linkedin,
            'github' => $this->resume->github,
            'professional_summary' => $this->resume->professional_summary,
            'nationality' => $this->resume->metadata['nationality'] ?? '',
            'date_of_birth' => $this->resume->metadata['date_of_birth'] ?? '',
            'marital_status' => $this->resume->metadata['marital_status'] ?? '',
            'driving_license' => $this->resume->metadata['driving_license'] ?? '',
        ];

        // Initialize template settings
        $this->selectedTemplate = $this->resume->selected_template;
        $this->selectedColorScheme = $this->resume->color_scheme;
        $this->selectedFont = $this->resume->font_family;
        
        // Reset all form arrays
        $this->resetAllForms();
    }

    protected function initializeSectionSettings()
    {
        $this->sectionVisibility = $this->resume->section_visibility ?? $this->getDefaultSectionVisibility();
        $this->sectionOrder = $this->resume->section_order ?? $this->getDefaultSectionOrder();
    }

    protected function getDefaultSectionVisibility()
    {
        return [
            'personal' => true,
            'summary' => true,
            'experience' => true,
            'education' => true,
            'skills' => true,
            'projects' => false,
            'certifications' => false,
            'languages' => false,
            'volunteer' => false,
            'publications' => false,
            'awards' => false,
            'references' => false
        ];
    }

    protected function getDefaultSectionOrder()
    {
        return array_keys($this->sections);
    }


    // Navigation methods
    public function setActiveSection($section)
    {
        $this->activeSection = $section;
        $this->resetErrorBag();
    }

    public function setViewMode($mode)
    {
        if ($mode === 'preview' && !$this->resume->canBeExported()) {
            session()->flash('error', 'Please complete required sections before previewing.');
            return;
        }
        $this->viewMode = $mode;
    }

    // Personal Information Methods
    public function savePersonalInfo()
    {
        $this->validate([
            'personalForm.full_name' => 'required|string|max:255',
            'personalForm.professional_title' => 'required|string|max:255',
            'personalForm.email' => 'required|email|max:255',
            'personalForm.professional_summary' => 'required|string|max:1000',
        ]);

        $metadata = $this->resume->metadata ?? [];
        $metadata['nationality'] = $this->personalForm['nationality'] ?? null;
        $metadata['date_of_birth'] = $this->personalForm['date_of_birth'] ?? null;
        $metadata['marital_status'] = $this->personalForm['marital_status'] ?? null;
        $metadata['driving_license'] = $this->personalForm['driving_license'] ?? null;

        $updateData = array_merge($this->personalForm, ['metadata' => $metadata]);
        $this->resume->update($updateData);
        
        session()->flash('success', 'Personal information saved successfully!');
    }

    public function uploadProfileImage()
    {
        $this->validate(['profileImageUpload' => 'required|image|max:2048']);

        if ($this->resume->profile_image) {
            Storage::delete($this->resume->profile_image);
        }

        $filename = 'resume_' . auth()->id() . '_' . time() . '.jpg';
        $path = 'profile_images/' . $filename;

        $resizedImage = Image::make($this->profileImageUpload->getRealPath())
            ->fit(400, 400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 85);

        Storage::put($path, $resizedImage);
        
        $this->resume->update(['profile_image' => $path]);
        $this->profileImageUpload = null;
        session()->flash('success', 'Profile image updated successfully!');
    }

    // Experience Methods
    public function addExperience()
    {
        $this->validate([
            'experienceForm.position' => 'required|string|max:255',
            'experienceForm.company' => 'required|string|max:255',
            'experienceForm.start_date' => 'required|date',
        ]);

        $experiences = $this->resume->work_experience ?? [];
        $this->experienceForm['id'] = Str::uuid();
        $experiences[] = $this->experienceForm;
        
        $this->resume->update(['work_experience' => $experiences]);
        $this->resetExperienceForm();
        session()->flash('success', 'Work experience added successfully!');
    }

    public function editExperience($index)
    {
        $experiences = $this->resume->work_experience ?? [];
        if (isset($experiences[$index])) {
            $this->experienceForm = $experiences[$index];
            $this->editingExperienceIndex = $index;
        }
    }

    public function updateExperience()
    {
        if ($this->editingExperienceIndex !== null) {
            $experiences = $this->resume->work_experience ?? [];
            $experiences[$this->editingExperienceIndex] = $this->experienceForm;
            
            $this->resume->update(['work_experience' => $experiences]);
            $this->resetExperienceForm();
            session()->flash('success', 'Work experience updated successfully!');
        }
    }

    public function deleteExperience($index)
    {
        $experiences = $this->resume->work_experience ?? [];
        unset($experiences[$index]);
        
        $this->resume->update(['work_experience' => array_values($experiences)]);
        session()->flash('success', 'Work experience deleted successfully!');
    }

    protected function resetExperienceForm()
    {
        $this->experienceForm = [];
        $this->editingExperienceIndex = null;
    }

    // Education Methods
    public function addEducation()
    {
        $this->validate([
            'educationForm.degree' => 'required|string|max:255',
            'educationForm.institution' => 'required|string|max:255',
            'educationForm.start_date' => 'required|date',
        ]);

        $education = $this->resume->education ?? [];
        $this->educationForm['id'] = Str::uuid();
        $education[] = $this->educationForm;
        
        $this->resume->update(['education' => $education]);
        $this->resetEducationForm();
        session()->flash('success', 'Education added successfully!');
    }

    public function editEducation($index)
    {
        $education = $this->resume->education ?? [];
        if (isset($education[$index])) {
            $this->educationForm = $education[$index];
            $this->editingEducationIndex = $index;
        }
    }

    public function updateEducation()
    {
        if ($this->editingEducationIndex !== null) {
            $education = $this->resume->education ?? [];
            $education[$this->editingEducationIndex] = $this->educationForm;
            
            $this->resume->update(['education' => $education]);
            $this->resetEducationForm();
            session()->flash('success', 'Education updated successfully!');
        }
    }

    public function deleteEducation($index)
    {
        $education = $this->resume->education ?? [];
        unset($education[$index]);
        
        $this->resume->update(['education' => array_values($education)]);
        session()->flash('success', 'Education deleted successfully!');
    }

    protected function resetEducationForm()
    {
        $this->educationForm = [];
        $this->editingEducationIndex = null;
    }

    // Skills Methods
    public function addSkill()
    {
        $this->validate([
            'skillForm.name' => 'required|string|max:255',
            'skillForm.proficiency' => 'required|integer|min:1|max:100',
        ]);

        $skills = $this->resume->skills ?? [];
        $this->skillForm['id'] = Str::uuid();
        $skills[] = $this->skillForm;
        
        $this->resume->update(['skills' => $skills]);
        $this->resetSkillForm();
        session()->flash('success', 'Skill added successfully!');
    }

    public function editSkill($index)
    {
        $skills = $this->resume->skills ?? [];
        if (isset($skills[$index])) {
            $this->skillForm = $skills[$index];
            $this->editingSkillIndex = $index;
        }
    }

    public function updateSkill()
    {
        if ($this->editingSkillIndex !== null) {
            $skills = $this->resume->skills ?? [];
            $skills[$this->editingSkillIndex] = $this->skillForm;
            
            $this->resume->update(['skills' => $skills]);
            $this->resetSkillForm();
            session()->flash('success', 'Skill updated successfully!');
        }
    }

    public function deleteSkill($index)
    {
        $skills = $this->resume->skills ?? [];
        unset($skills[$index]);
        
        $this->resume->update(['skills' => array_values($skills)]);
        session()->flash('success', 'Skill deleted successfully!');
    }

    protected function resetSkillForm()
    {
        $this->skillForm = [];
        $this->editingSkillIndex = null;
    }

    // Projects Methods
    public function addProject()
    {
        $this->validate([
            'projectForm.name' => 'required|string|max:255',
        ]);

        $projects = $this->resume->projects ?? [];
        $this->projectForm['id'] = Str::uuid();
        $projects[] = $this->projectForm;
        
        $this->resume->update(['projects' => $projects]);
        $this->resetProjectForm();
        session()->flash('success', 'Project added successfully!');
    }

    public function editProject($index)
    {
        $projects = $this->resume->projects ?? [];
        if (isset($projects[$index])) {
            $this->projectForm = $projects[$index];
            $this->editingProjectIndex = $index;
        }
    }

    public function updateProject()
    {
        if ($this->editingProjectIndex !== null) {
            $projects = $this->resume->projects ?? [];
            $projects[$this->editingProjectIndex] = $this->projectForm;
            
            $this->resume->update(['projects' => $projects]);
            $this->resetProjectForm();
            session()->flash('success', 'Project updated successfully!');
        }
    }

    public function deleteProject($index)
    {
        $projects = $this->resume->projects ?? [];
        unset($projects[$index]);
        
        $this->resume->update(['projects' => array_values($projects)]);
        session()->flash('success', 'Project deleted successfully!');
    }

    protected function resetProjectForm()
    {
        $this->projectForm = [];
        $this->editingProjectIndex = null;
    }

    // Certifications Methods
    public function addCertification()
    {
        $this->validate([
            'certificationForm.name' => 'required|string|max:255',
            'certificationForm.issuer' => 'required|string|max:255',
        ]);

        $certifications = $this->resume->certifications ?? [];
        $this->certificationForm['id'] = Str::uuid();
        $certifications[] = $this->certificationForm;
        
        $this->resume->update(['certifications' => $certifications]);
        $this->resetCertificationForm();
        session()->flash('success', 'Certification added successfully!');
    }

    public function editCertification($index)
    {
        $certifications = $this->resume->certifications ?? [];
        if (isset($certifications[$index])) {
            $this->certificationForm = $certifications[$index];
            $this->editingCertificationIndex = $index;
        }
    }

    public function updateCertification()
    {
        if ($this->editingCertificationIndex !== null) {
            $certifications = $this->resume->certifications ?? [];
            $certifications[$this->editingCertificationIndex] = $this->certificationForm;
            
            $this->resume->update(['certifications' => $certifications]);
            $this->resetCertificationForm();
            session()->flash('success', 'Certification updated successfully!');
        }
    }

    public function deleteCertification($index)
    {
        $certifications = $this->resume->certifications ?? [];
        unset($certifications[$index]);
        
        $this->resume->update(['certifications' => array_values($certifications)]);
        session()->flash('success', 'Certification deleted successfully!');
    }

    protected function resetCertificationForm()
    {
        $this->certificationForm = [];
        $this->editingCertificationIndex = null;
    }

    // Languages Methods
    public function addLanguage()
    {
        $this->validate([
            'languageForm.language' => 'required|string|max:255',
            'languageForm.proficiency' => 'required|string|max:255',
        ]);

        $languages = $this->resume->languages ?? [];
        $this->languageForm['id'] = Str::uuid();
        $languages[] = $this->languageForm;
        
        $this->resume->update(['languages' => $languages]);
        $this->resetLanguageForm();
        session()->flash('success', 'Language added successfully!');
    }

    public function editLanguage($index)
    {
        $languages = $this->resume->languages ?? [];
        if (isset($languages[$index])) {
            $this->languageForm = $languages[$index];
            $this->editingLanguageIndex = $index;
        }
    }

    public function updateLanguage()
    {
        if ($this->editingLanguageIndex !== null) {
            $languages = $this->resume->languages ?? [];
            $languages[$this->editingLanguageIndex] = $this->languageForm;
            
            $this->resume->update(['languages' => $languages]);
            $this->resetLanguageForm();
            session()->flash('success', 'Language updated successfully!');
        }
    }

    public function deleteLanguage($index)
    {
        $languages = $this->resume->languages ?? [];
        unset($languages[$index]);
        
        $this->resume->update(['languages' => array_values($languages)]);
        session()->flash('success', 'Language deleted successfully!');
    }

    protected function resetLanguageForm()
    {
        $this->languageForm = [];
        $this->editingLanguageIndex = null;
    }

    // Volunteer Work Methods
    public function addVolunteer()
    {
        $this->validate([
            'volunteerForm.organization' => 'required|string|max:255',
            'volunteerForm.position' => 'required|string|max:255',
            'volunteerForm.start_date' => 'required|date',
        ]);

        $volunteer = $this->resume->volunteer ?? [];
        $this->volunteerForm['id'] = Str::uuid();
        $volunteer[] = $this->volunteerForm;
        
        $this->resume->update(['volunteer' => $volunteer]);
        $this->resetVolunteerForm();
        session()->flash('success', 'Volunteer experience added successfully!');
    }

    public function editVolunteer($index)
    {
        $volunteer = $this->resume->volunteer ?? [];
        if (isset($volunteer[$index])) {
            $this->volunteerForm = $volunteer[$index];
            $this->editingVolunteerIndex = $index;
        }
    }

    public function updateVolunteer()
    {
        if ($this->editingVolunteerIndex !== null) {
            $volunteer = $this->resume->volunteer ?? [];
            $volunteer[$this->editingVolunteerIndex] = $this->volunteerForm;
            
            $this->resume->update(['volunteer' => $volunteer]);
            $this->resetVolunteerForm();
            session()->flash('success', 'Volunteer experience updated successfully!');
        }
    }

    public function deleteVolunteer($index)
    {
        $volunteer = $this->resume->volunteer ?? [];
        unset($volunteer[$index]);
        
        $this->resume->update(['volunteer' => array_values($volunteer)]);
        session()->flash('success', 'Volunteer experience deleted successfully!');
    }

    protected function resetVolunteerForm()
    {
        $this->volunteerForm = [];
        $this->editingVolunteerIndex = null;
    }

    // Publications Methods
    public function addPublication()
    {
        $this->validate([
            'publicationForm.title' => 'required|string|max:255',
        ]);

        $publications = $this->resume->publications ?? [];
        $this->publicationForm['id'] = Str::uuid();
        $publications[] = $this->publicationForm;
        
        $this->resume->update(['publications' => $publications]);
        $this->resetPublicationForm();
        session()->flash('success', 'Publication added successfully!');
    }

    public function editPublication($index)
    {
        $publications = $this->resume->publications ?? [];
        if (isset($publications[$index])) {
            $this->publicationForm = $publications[$index];
            $this->editingPublicationIndex = $index;
        }
    }

    public function updatePublication()
    {
        if ($this->editingPublicationIndex !== null) {
            $publications = $this->resume->publications ?? [];
            $publications[$this->editingPublicationIndex] = $this->publicationForm;
            
            $this->resume->update(['publications' => $publications]);
            $this->resetPublicationForm();
            session()->flash('success', 'Publication updated successfully!');
        }
    }

    public function deletePublication($index)
    {
        $publications = $this->resume->publications ?? [];
        unset($publications[$index]);
        
        $this->resume->update(['publications' => array_values($publications)]);
        session()->flash('success', 'Publication deleted successfully!');
    }

    protected function resetPublicationForm()
    {
        $this->publicationForm = [];
        $this->editingPublicationIndex = null;
    }

    // Awards Methods
    public function addAward()
    {
        $this->validate([
            'awardForm.title' => 'required|string|max:255',
            'awardForm.issuer' => 'required|string|max:255',
        ]);

        $awards = $this->resume->awards ?? [];
        $this->awardForm['id'] = Str::uuid();
        $awards[] = $this->awardForm;
        
        $this->resume->update(['awards' => $awards]);
        $this->resetAwardForm();
        session()->flash('success', 'Award added successfully!');
    }

    public function editAward($index)
    {
        $awards = $this->resume->awards ?? [];
        if (isset($awards[$index])) {
            $this->awardForm = $awards[$index];
            $this->editingAwardIndex = $index;
        }
    }

    public function updateAward()
    {
        if ($this->editingAwardIndex !== null) {
            $awards = $this->resume->awards ?? [];
            $awards[$this->editingAwardIndex] = $this->awardForm;
            
            $this->resume->update(['awards' => $awards]);
            $this->resetAwardForm();
            session()->flash('success', 'Award updated successfully!');
        }
    }

    public function deleteAward($index)
    {
        $awards = $this->resume->awards ?? [];
        unset($awards[$index]);
        
        $this->resume->update(['awards' => array_values($awards)]);
        session()->flash('success', 'Award deleted successfully!');
    }

    protected function resetAwardForm()
    {
        $this->awardForm = [];
        $this->editingAwardIndex = null;
    }

    // References Methods
    public function addReference()
    {
        $this->validate([
            'referenceForm.name' => 'required|string|max:255',
        ]);

        $references = $this->resume->references ?? [];
        $this->referenceForm['id'] = Str::uuid();
        $references[] = $this->referenceForm;
        
        $this->resume->update(['references' => $references]);
        $this->resetReferenceForm();
        session()->flash('success', 'Reference added successfully!');
    }

    public function editReference($index)
    {
        $references = $this->resume->references ?? [];
        if (isset($references[$index])) {
            $this->referenceForm = $references[$index];
            $this->editingReferenceIndex = $index;
        }
    }

    public function updateReference()
    {
        if ($this->editingReferenceIndex !== null) {
            $references = $this->resume->references ?? [];
            $references[$this->editingReferenceIndex] = $this->referenceForm;
            
            $this->resume->update(['references' => $references]);
            $this->resetReferenceForm();
            session()->flash('success', 'Reference updated successfully!');
        }
    }

    public function deleteReference($index)
    {
        $references = $this->resume->references ?? [];
        unset($references[$index]);
        
        $this->resume->update(['references' => array_values($references)]);
        session()->flash('success', 'Reference deleted successfully!');
    }

    protected function resetReferenceForm()
    {
        $this->referenceForm = [];
        $this->editingReferenceIndex = null;
    }

    // Template & Styling Methods
    public function updateTemplate($templateKey)
    {
        if (!array_key_exists($templateKey, $this->templates)) {
            return;
        }

        $template = $this->templates[$templateKey];
        
        // Check premium access
        if ($template['is_premium'] && !auth()->user()->is_premium) {
            session()->flash('info', 'This is a premium template. Upgrade to access premium features.');
            return;
        }

        $this->selectedTemplate = $templateKey;
        $this->resume->update(['selected_template' => $templateKey]);
        session()->flash('success', 'Template updated successfully!');
    }

    public function updateColorScheme($schemeKey)
    {
        if (!array_key_exists($schemeKey, $this->colorSchemes)) {
            return;
        }

        $this->selectedColorScheme = $schemeKey;
        $this->resume->update(['color_scheme' => $schemeKey]);
        session()->flash('success', 'Color scheme updated successfully!');
    }

    public function updateFont($fontKey)
    {
        if (!array_key_exists($fontKey, $this->fonts)) {
            return;
        }

        $this->selectedFont = $fontKey;
        $this->resume->update(['font_family' => $fontKey]);
        session()->flash('success', 'Font updated successfully!');
    }

    // Section Visibility & Ordering
    public function toggleSectionVisibility($section)
    {
        $this->sectionVisibility[$section] = !$this->sectionVisibility[$section];
        $this->resume->update(['section_visibility' => $this->sectionVisibility]);
        session()->flash('success', 'Section visibility updated!');
    }

    public function reorderSections($newOrder)
    {
        $this->sectionOrder = $newOrder;
        $this->resume->update(['section_order' => $newOrder]);
        session()->flash('success', 'Section order updated!');
    }

    // Public Access Methods
    public function togglePublicAccess()
    {
        $this->resume->update(['is_public' => !$this->resume->is_public]);
        
        if ($this->resume->is_public) {
            session()->flash('success', 'Resume is now public and shareable!');
        } else {
            session()->flash('success', 'Resume is now private.');
        }
    }

    // AI Enhancement Methods
    public function generateAISuggestions($section)
    {
        if (!config('services.openai.key')) {
            session()->flash('error', 'AI features are not available.');
            return;
        }

        $this->aiProcessing = true;

        try {
            $prompt = $this->buildAIPrompt($section);
            $response = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional resume consultant.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $aiSuggestion = $response->json()['choices'][0]['message']['content'];
                $this->aiSuggestions[$section] = $aiSuggestion;
                session()->flash('success', 'AI suggestions generated successfully!');
            } else {
                session()->flash('error', 'Failed to generate AI suggestions.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'AI service is temporarily unavailable.');
        }

        $this->aiProcessing = false;
    }

    protected function buildAIPrompt($section)
    {
        $sectionData = match($section) {
            'personal' => $this->personalForm['professional_summary'] ?? '',
            'experience' => json_encode($this->resume->work_experience ?? []),
            'skills' => json_encode($this->resume->skills ?? []),
            default => ''
        };

        return "Improve the following {$section} section for a professional resume: {$sectionData}. Provide specific, actionable suggestions.";
    }

    public function enhanceWithAI($section, $field = null)
    {
        if (!config('services.openai.key')) {
            session()->flash('error', 'AI features are not available.');
            return;
        }

        $this->aiProcessing = true;

        try {
            $currentText = $field ? ($this->{$section . 'Form'}[$field] ?? '') : '';
            $prompt = "Enhance this {$section} text for a professional resume: {$currentText}";
            
            $response = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional resume writer. Provide enhanced, professional text.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $enhancedText = $response->json()['choices'][0]['message']['content'];
                
                if ($field) {
                    $formProperty = $section . 'Form';
                    $this->{$formProperty}[$field] = trim($enhancedText, '"');
                } else {
                    $this->aiEnhancedText = $enhancedText;
                }
                
                session()->flash('success', 'Text enhanced with AI!');
            } else {
                session()->flash('error', 'Failed to enhance text with AI.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'AI service is temporarily unavailable.');
        }

        $this->aiProcessing = false;
    }

    // Export Methods
    public function exportPDF()
    {
        if (!$this->resume->canBeExported()) {
            session()->flash('error', 'Please complete required sections before exporting.');
            return;
        }

        try {
            $html = view('livewire.career.resume.pdf-template', [
                'resume' => $this->resume,
                'selectedTemplate' => $this->selectedTemplate,
                'selectedColorScheme' => $this->selectedColorScheme,
                'selectedFont' => $this->selectedFont,
                'colorSchemes' => $this->colorSchemes,
            ])->render();

            $pdf = Pdf::loadHTML($html)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                ]);

            $this->resume->incrementDownloadCount();
            $filename = $this->resume->generateFilename('pdf');

            return response()->streamDownload(
                fn () => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate PDF. Please try again.');
        }
    }

    public function exportJSON()
    {
        $data = [
            'personal_info' => $this->personalForm,
            'work_experience' => $this->resume->work_experience,
            'education' => $this->resume->education,
            'skills' => $this->resume->skills,
            'projects' => $this->resume->projects,
            'certifications' => $this->resume->certifications,
            'languages' => $this->resume->languages,
            'volunteer' => $this->resume->volunteer,
            'publications' => $this->resume->publications,
            'awards' => $this->resume->awards,
            'references' => $this->resume->references,
            'metadata' => $this->resume->metadata,
        ];

        $filename = $this->resume->generateFilename('json');
        
        return response()->streamDownload(
            fn () => print(json_encode($data, JSON_PRETTY_PRINT)),
            $filename,
            ['Content-Type' => 'application/json']
        );
    }

    // Import Methods
    public function importFromLinkedIn()
    {
        // This would integrate with LinkedIn API
        session()->flash('info', 'LinkedIn import feature coming soon!');
    }

    public function importFromJSON()
    {
        $this->validate(['documentUpload' => 'required|file|mimes:json|max:2048']);

        try {
            $content = file_get_contents($this->documentUpload->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                session()->flash('error', 'Invalid JSON file.');
                return;
            }

            // Update resume with imported data
            $this->resume->update([
                'work_experience' => $data['work_experience'] ?? [],
                'education' => $data['education'] ?? [],
                'skills' => $data['skills'] ?? [],
                'projects' => $data['projects'] ?? [],
                'certifications' => $data['certifications'] ?? [],
                'languages' => $data['languages'] ?? [],
                'volunteer' => $data['volunteer'] ?? [],
                'publications' => $data['publications'] ?? [],
                'awards' => $data['awards'] ?? [],
                'references' => $data['references'] ?? [],
            ]);

            // Update personal info if available
            if (isset($data['personal_info'])) {
                $this->personalForm = array_merge($this->personalForm, $data['personal_info']);
                $this->savePersonalInfo();
            }

            $this->documentUpload = null;
            session()->flash('success', 'Resume data imported successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to import resume data.');
        }
    }

    // Utility Methods
    protected function resetAllForms()
    {
        $this->experienceForm = [];
        $this->educationForm = [];
        $this->skillForm = [];
        $this->projectForm = [];
        $this->certificationForm = [];
        $this->languageForm = [];
        $this->referenceForm = [];
        $this->volunteerForm = [];
        $this->publicationForm = [];
        $this->awardForm = [];
        $this->customSectionForm = [];
    }

    public function duplicateResume()
    {
        $newResume = $this->resume->replicate();
        $newResume->public_slug = null;
        $newResume->is_public = false;
        $newResume->view_count = 0;
        $newResume->download_count = 0;
        $newResume->save();

        session()->flash('success', 'Resume duplicated successfully!');
        return redirect()->route('resume.builder');
    }
    public function getCompletionPercentageProperty()
    {
        return $this->resume->completion_percentage; // Use attribute accessor
    }

    public function getQualityScoreProperty()
    {
        return $this->resume->quality_score; // Use attribute accessor
    }

    public function getSuggestionsProperty()
    {
        return $this->resume->getSuggestions(); // This method exists
    }
    public function render()
    {
        return view('livewire.career.resume-builder', [
            'completionPercentage' => $this->completionPercentage,
            'qualityScore' => $this->qualityScore,
            'suggestions' => $this->suggestions,
            'templates' => $this->templates,
            'colorSchemes' => $this->colorSchemes,
            'fonts' => $this->fonts,
            'skillCategories' => $this->skillCategories,
        ]);
    }
}