@props(['job', 'applied' => false])

@php
    $careerEligibility = $job->requirements_reviewed_at && auth()->user()?->is_active && auth()->user()?->hasVerifiedEmail()
        ? app(\App\Services\Career\JobEligibilityService::class)->evaluate(auth()->user(), $job) : null;
@endphp

@if(!$job->isActive() || $job->application_deadline?->isPast())
    <x-secondary-button disabled {{ $attributes }}>Applications closed</x-secondary-button>
@elseif($applied)
    <x-secondary-button disabled {{ $attributes }}>Applied</x-secondary-button>
@elseif($careerEligibility && !$careerEligibility['can_apply'])
    <a href="{{ route('career.centre', ['job' => $job->id]) }}" {{ $attributes->class(['bk-button bk-button-secondary']) }}>Review eligibility</a>
@elseif($job->application_method === 'internal')
    <x-primary-button type="button" wire:click="openApplicationModal({{ $job->id }})" {{ $attributes }}>Apply now</x-primary-button>
@elseif($job->application_destination)
    <a href="{{ $job->application_destination }}" @if($job->application_method === 'external_link') target="_blank" rel="noopener noreferrer" @endif {{ $attributes->class(['bk-button bk-button-primary']) }}>
        {{ match($job->application_method) { 'email' => 'Apply by email', 'phone' => 'Call to apply', default => 'Employer application' } }}
    </a>
@else
    <p {{ $attributes->class(['text-sm text-content-muted']) }}>Application details are unavailable.</p>
@endif
