@component('mail::message')
# Certificate Request Rejected

Your certificate request for **{{ $certificate->course->title }}** has been rejected.

**Reason for rejection:**  
{{ $certificate->rejection_reason }}

**What to do next:**  
- Review the reason for rejection  
- Make any necessary corrections  
- Resubmit your request  

@component('mail::button', ['url' => $url])
Request Certificate Again
@endcomponent

If you have any questions, please reply to this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent