<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resume->full_name ?? 'Professional Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: '{{ ucfirst(str_replace("-", " ", $resume->font_family ?? "inter")) }}', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #374151;
            background: white;
        }

        .resume-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            display: flex;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            width: 35%;
            background:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#1e293b' }}
            ;
            color: white;
            padding: 30px 25px;
            position: relative;
        }

        .main-content {
            width: 65%;
            padding: 30px 35px;
            background: white;
        }

        /* Header Section */
        .profile-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.2);
            margin: 0 auto 20px;
            display: block;
        }

        .name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .title {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 400;
            margin-bottom: 20px;
        }

        /* Contact Information */
        .contact-info {
            margin-bottom: 35px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 11px;
        }

        .contact-icon {
            width: 16px;
            text-align: center;
            margin-right: 12px;
            opacity: 0.8;
        }

        /* Section Headers */
        .section-header {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar .section-header {
            color: white;
            border-bottom-color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
        }

        /* Skills Section */
        .skill-item {
            margin-bottom: 15px;
        }

        .skill-name {
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 6px;
            display: flex;
            justify-content: between;
        }

        .skill-level {
            font-size: 10px;
            opacity: 0.8;
            margin-left: auto;
        }

        .skill-bar {
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            overflow: hidden;
        }

        .skill-progress {
            height: 100%;
            background: white;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Languages */
        .language-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .language-name {
            font-weight: 500;
        }

        .language-level {
            opacity: 0.8;
            font-size: 10px;
        }

        /* Main Content Styles */
        .professional-summary {
            margin-bottom: 35px;
            font-size: 13px;
            line-height: 1.6;
            text-align: justify;
        }

        /* Experience and Education */
        .experience-item,
        .education-item {
            margin-bottom: 25px;
            position: relative;
            padding-left: 20px;
        }

        .experience-item::before,
        .education-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
        }

        .job-title,
        .degree-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .company-name,
        .institution-name {
            font-size: 12px;
            font-weight: 500;
            color:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            margin-bottom: 2px;
        }

        .date-range {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .job-description,
        .education-description {
            font-size: 11px;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 8px;
        }

        /* Projects */
        .project-item {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-left: 3px solid
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            border-radius: 0 6px 6px 0;
        }

        .project-title {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1f2937;
        }

        .project-description {
            font-size: 11px;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .project-tech {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .tech-tag {
            background:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 500;
        }

        /* Certifications */
        .cert-item {
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .cert-item:last-child {
            border-bottom: none;
        }

        .cert-name {
            font-size: 12px;
            font-weight: 600;
            color: #1f2937;
        }

        .cert-issuer {
            font-size: 11px;
            color:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            font-weight: 500;
        }

        .cert-date {
            font-size: 10px;
            color: #6b7280;
        }

        /* References */
        .reference-item {
            margin-bottom: 20px;
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
        }

        .reference-name {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .reference-position {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .reference-contact {
            font-size: 9px;
            color: #374151;
        }

        /* Awards */
        .award-item {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .award-icon {
            width: 16px;
            height: 16px;
            background:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            border-radius: 50%;
            margin-right: 10px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .award-content {
            flex: 1;
        }

        .award-title {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .award-issuer {
            font-size: 10px;
            color:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            font-weight: 500;
        }

        /* Publications */
        .publication-item {
            margin-bottom: 18px;
            padding-left: 15px;
            position: relative;
        }

        .publication-item::before {
            content: '"';
            position: absolute;
            left: 0;
            top: 0;
            font-size: 20px;
            font-weight: bold;
            color:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            line-height: 1;
        }

        .publication-title {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 4px;
            font-style: italic;
        }

        .publication-authors {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .publication-journal {
            font-size: 10px;
            color:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            font-weight: 500;
        }

        /* Volunteer Work */
        .volunteer-item {
            margin-bottom: 20px;
            padding-left: 15px;
            position: relative;
        }

        .volunteer-item::before {
            content: '♥';
            position: absolute;
            left: 0;
            top: 4px;
            color:
                {{ $resume->colorSchemes[$resume->color_scheme]['primary'] ?? '#4f46e5' }}
            ;
            font-size: 12px;
        }

        /* Utilities */
        .mb-1 {
            margin-bottom: 4px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mb-5 {
            margin-bottom: 20px;
        }

        .text-sm {
            font-size: 11px;
        }

        .text-xs {
            font-size: 10px;
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }

        /* Print Styles */
        @media print {
            .resume-container {
                width: 100% !important;
                box-shadow: none !important;
                margin: 0 !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Page Break Control */
        .page-break-inside-avoid {
            page-break-inside: avoid;
        }

        .page-break-before {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="resume-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Profile Section -->
            <div class="profile-section">
                @if($resume->profile_image_url)
                    <img src="{{ $resume->profile_image_url }}" alt="Profile" class="profile-image">
                @endif
                <h1 class="name">{{ $resume->full_name ?? 'Your Name' }}</h1>
                @if($resume->professional_title)
                    <p class="title">{{ $resume->professional_title }}</p>
                @endif
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                @if($resume->email)
                    <div class="contact-item">
                        <span class="contact-icon">✉</span>
                        <span>{{ $resume->email }}</span>
                    </div>
                @endif
                @if($resume->phone)
                    <div class="contact-item">
                        <span class="contact-icon">📞</span>
                        <span>{{ $resume->phone }}</span>
                    </div>
                @endif
                @if($resume->location)
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <span>{{ $resume->location }}</span>
                    </div>
                @endif
                @if($resume->website)
                    <div class="contact-item">
                        <span class="contact-icon">🌐</span>
                        <span>{{ $resume->website }}</span>
                    </div>
                @endif
                @if($resume->linkedin)
                    <div class="contact-item">
                        <span class="contact-icon">💼</span>
                        <span>LinkedIn</span>
                    </div>
                @endif
                @if($resume->github)
                    <div class="contact-item">
                        <span class="contact-icon">🔗</span>
                        <span>GitHub</span>
                    </div>
                @endif
            </div>

            <!-- Skills -->
            @if($resume->skills && count($resume->skills) > 0)
                <div class="mb-5">
                    <h3 class="section-header">Skills</h3>
                    @foreach(collect($resume->skills)->sortByDesc('proficiency') as $skill)
                        <div class="skill-item">
                            <div class="skill-name">
                                <span>{{ $skill['name'] ?? 'N/A' }}</span>
                                <span class="skill-level">{{ $skill['proficiency'] ?? 50 }}%</span>
                            </div>
                            <div class="skill-bar">
                                <div class="skill-progress" style="width: {{ $skill['proficiency'] ?? 50 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Languages -->
            @if($resume->languages && count($resume->languages) > 0)
                <div class="mb-5">
                    <h3 class="section-header">Languages</h3>
                    @foreach($resume->languages as $language)
                        <div class="language-item">
                            <span class="language-name">{{ $language['language'] ?? 'N/A' }}</span>
                            <span class="language-level">{{ $language['proficiency'] ?? 'N/A' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Certifications -->
            @if($resume->certifications && count($resume->certifications) > 0)
                <div class="mb-5">
                    <h3 class="section-header">Certifications</h3>
                    @foreach($resume->certifications as $cert)
                        <div class="cert-item">
                            <div class="cert-name">{{ $cert['name'] ?? 'N/A' }}</div>
                            <div class="cert-issuer">{{ $cert['issuer'] ?? 'N/A' }}</div>
                            @if(isset($cert['issue_date']))
                                <div class="cert-date">{{ \Carbon\Carbon::parse($cert['issue_date'])->format('M Y') }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Professional Summary -->
            @if($resume->professional_summary)
                <div class="professional-summary">
                    <h2 class="section-header">Professional Summary</h2>
                    <p>{{ $resume->professional_summary }}</p>
                </div>
            @endif

            <!-- Work Experience -->
            @if($resume->work_experience && count($resume->work_experience) > 0)
                <div class="mb-5 page-break-inside-avoid">
                    <h2 class="section-header">Work Experience</h2>
                    @foreach($resume->work_experience as $exp)
                        <div class="experience-item">
                            <div class="job-title">{{ $exp['position'] ?? 'N/A' }}</div>
                            <div class="company-name">{{ $exp['company'] ?? 'N/A' }}</div>
                            <div class="date-range">
                                @if(isset($exp['start_date']))
                                    {{ \Carbon\Carbon::parse($exp['start_date'])->format('M Y') }}
                                @endif
                                -
                                @if(isset($exp['current']) && $exp['current'])
                                    Present
                                @elseif(isset($exp['end_date']))
                                    {{ \Carbon\Carbon::parse($exp['end_date'])->format('M Y') }}
                                @else
                                    Present
                                @endif
                            </div>
                            @if(isset($exp['description']))
                                <div class="job-description">{{ $exp['description'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Education -->
            @if($resume->education && count($resume->education) > 0)
                <div class="mb-5 page-break-inside-avoid">
                    <h2 class="section-header">Education</h2>
                    @foreach($resume->education as $edu)
                        <div class="education-item">
                            <div class="degree-title">{{ $edu['degree'] ?? 'N/A' }}</div>
                            @if(isset($edu['field_of_study']))
                                <div class="text-sm mb-1">{{ $edu['field_of_study'] }}</div>
                            @endif
                            <div class="institution-name">{{ $edu['institution'] ?? 'N/A' }}</div>
                            <div class="date-range">
                                @if(isset($edu['start_date']))
                                    {{ \Carbon\Carbon::parse($edu['start_date'])->format('M Y') }}
                                @endif
                                @if(isset($edu['end_date']))
                                    - {{ \Carbon\Carbon::parse($edu['end_date'])->format('M Y') }}
                                @endif
                                @if(isset($edu['gpa']))
                                    | GPA: {{ $edu['gpa'] }}
                                @endif
                            </div>
                            @if(isset($edu['description']))
                                <div class="education-description">{{ $edu['description'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Projects -->
            @if($resume->projects && count($resume->projects) > 0)
                <div class="mb-5 page-break-inside-avoid">
                    <h2 class="section-header">Featured Projects</h2>
                    @foreach($resume->projects as $project)
                        <div class="project-item">
                            <div class="project-title">{{ $project['name'] ?? 'N/A' }}</div>
                            @if(isset($project['description']))
                                <div class="project-description">{{ $project['description'] }}</div>
                            @endif
                            @if(isset($project['technologies']))
                                <div class="project-tech">
                                    @foreach(explode(',', $project['technologies']) as $tech)
                                        <span class="tech-tag">{{ trim($tech) }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Awards -->
            @if($resume->awards && count($resume->awards) > 0)
                <div class="mb-5 page-break-inside-avoid">
                    <h2 class="section-header">Awards & Honors</h2>
                    @foreach($resume->awards as $award)
                        <div class="award-item">
                            <div class="award-icon"></div>
                            <div class="award-content">
                                <div class="award-title">{{ $award['title'] ?? 'N/A' }}</div>
                                <div class="award-issuer">{{ $award['issuer'] ?? 'N/A' }}</div>
                                @if(isset($award['date']))
                                    <div class="text-xs">{{ \Carbon\Carbon::parse($award['date'])->format('F Y') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Publications -->
            @if($resume->publications && count($resume->publications) > 0)
                <div class="mb-5 page-break-inside-avoid">
                    <h2 class="section-header">Publications</h2>
                    @foreach($resume->publications as $pub)
                        <div class="publication-item">
                            <div class="publication-title">{{ $pub['title'] ?? 'N/A' }}</div>
                            @if(isset($pub['authors']))
                                <div class="publication-authors">{{ $pub['authors'] }}</div>
                            @endif
                            @if(isset($pub['publication']))
                                <div class="publication-journal">{{ $pub['publication'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Volunteer Work -->
            @if($resume->volunteer && count($resume->volunteer) > 0)
                <div class="mb-5 page-break-inside-avoid">
                    <h2 class="section-header">Volunteer Experience</h2>
                    @foreach($resume->volunteer as $vol)
                        <div class="volunteer-item">
                            <div class="job-title">{{ $vol['position'] ?? 'N/A' }}</div>
                            <div class="company-name">{{ $vol['organization'] ?? 'N/A' }}</div>
                            <div class="date-range">
                                @if(isset($vol['start_date']))
                                    {{ \Carbon\Carbon::parse($vol['start_date'])->format('M Y') }}
                                @endif
                                -
                                @if(isset($vol['current']) && $vol['current'])
                                    Present
                                @elseif(isset($vol['end_date']))
                                    {{ \Carbon\Carbon::parse($vol['end_date'])->format('M Y') }}
                                @else
                                    Present
                                @endif
                            </div>
                            @if(isset($vol['description']))
                                <div class="job-description">{{ $vol['description'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- References -->
            @if($resume->references && count($resume->references) > 0)
                <div class="mb-5">
                    <h2 class="section-header">References</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        @foreach($resume->references as $ref)
                            <div class="reference-item">
                                <div class="reference-name">{{ $ref['name'] ?? 'N/A' }}</div>
                                @if(isset($ref['position']))
                                    <div class="reference-position">{{ $ref['position'] }}</div>
                                @endif
                                @if(isset($ref['company']))
                                    <div class="reference-position">{{ $ref['company'] }}</div>
                                @endif
                                <div class="reference-contact">
                                    @if(isset($ref['email']))
                                        <div>✉ {{ $ref['email'] }}</div>
                                    @endif
                                    @if(isset($ref['phone']))
                                        <div>📞 {{ $ref['phone'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>

</html>