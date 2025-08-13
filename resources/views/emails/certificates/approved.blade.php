@component('mail::message')
# Certificate Approved

Your certificate for **{{ $certificate->course->title }}** has been approved and is now available for download.

**Certificate Details:**  
- Certificate ID: {{ $certificate->uuid }}  
- Verification Code: {{ $certificate->verification_code }}  
- Issue Date: {{ $certificate->issue_date->format('M d, Y') }}  

@component('mail::button', ['url' => $url])
View My Certificates
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent