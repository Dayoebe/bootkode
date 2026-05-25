<?php

namespace App\Livewire\SystemManagement;

use App\Models\Admin\NewsletterInteraction;
use App\Models\Marketplace\PaystackTransaction;
use App\Models\System\ObservabilityEvent;
use App\Services\ObservabilityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', [
    'title' => 'Production Observability',
    'description' => 'Monitor production errors, jobs, mail, webhooks, slow pages, and broken routes',
    'icon' => 'fas fa-heart-pulse',
    'active' => 'production.observability',
])]
class ProductionObservability extends Component
{
    use WithPagination;

    public string $activeTab = 'overview';
    public string $typeFilter = 'all';
    public string $statusFilter = ObservabilityEvent::STATUS_OPEN;
    public string $severityFilter = 'all';
    public string $search = '';

    public function mount(): void
    {
        abort_unless(Auth::user()?->hasAnyRole(['super_admin', 'academy_admin']), 403);
    }

    public function updated($property): void
    {
        if (in_array($property, ['typeFilter', 'statusFilter', 'severityFilter', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function showTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->typeFilter = match ($tab) {
            'errors' => ObservabilityEvent::TYPE_ERROR,
            'jobs' => ObservabilityEvent::TYPE_FAILED_JOB,
            'mail' => ObservabilityEvent::TYPE_MAIL_FAILURE,
            'webhooks' => ObservabilityEvent::TYPE_WEBHOOK_FAILURE,
            'slow' => ObservabilityEvent::TYPE_SLOW_PAGE,
            'routes' => ObservabilityEvent::TYPE_BROKEN_ROUTE,
            default => 'all',
        };
        $this->resetPage();
    }

    public function resolveEvent(int $eventId): void
    {
        $event = ObservabilityEvent::findOrFail($eventId);
        $event->update([
            'status' => ObservabilityEvent::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        Auth::user()?->logCustomActivity('Resolved observability event', ['event_id' => $eventId]);
        $this->dispatch('notify', 'Event marked as resolved.', 'success');
    }

    public function reopenEvent(int $eventId): void
    {
        $event = ObservabilityEvent::findOrFail($eventId);
        $event->update([
            'status' => ObservabilityEvent::STATUS_OPEN,
            'resolved_at' => null,
            'resolved_by' => null,
        ]);

        Auth::user()?->logCustomActivity('Reopened observability event', ['event_id' => $eventId]);
        $this->dispatch('notify', 'Event reopened.', 'success');
    }

    public function ignoreEvent(int $eventId): void
    {
        $event = ObservabilityEvent::findOrFail($eventId);
        $event->update([
            'status' => ObservabilityEvent::STATUS_IGNORED,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        Auth::user()?->logCustomActivity('Ignored observability event', ['event_id' => $eventId]);
        $this->dispatch('notify', 'Event ignored.', 'success');
    }

    public function render(ObservabilityService $observability)
    {
        $eventsQuery = ObservabilityEvent::query()
            ->with(['user:id,name,email', 'resolver:id,name,email'])
            ->when($this->typeFilter !== 'all', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->severityFilter !== 'all', fn ($query) => $query->where('severity', $this->severityFilter))
            ->when($this->search !== '', function ($query) {
                $query->where(function ($nested) {
                    $nested->where('summary', 'like', '%' . $this->search . '%')
                        ->orWhere('message', 'like', '%' . $this->search . '%')
                        ->orWhere('url', 'like', '%' . $this->search . '%')
                        ->orWhere('source', 'like', '%' . $this->search . '%');
                });
            });

        $events = (clone $eventsQuery)
            ->latest('last_seen_at')
            ->paginate(10);

        $summary = [
            'open_errors' => ObservabilityEvent::where('status', ObservabilityEvent::STATUS_OPEN)
                ->whereIn('type', [ObservabilityEvent::TYPE_ERROR, ObservabilityEvent::TYPE_MAIL_FAILURE, ObservabilityEvent::TYPE_WEBHOOK_FAILURE])
                ->count(),
            'failed_jobs' => $this->failedJobsCount(),
            'mail_failures' => $this->mailFailuresCount(),
            'webhook_failures' => ObservabilityEvent::ofType(ObservabilityEvent::TYPE_WEBHOOK_FAILURE)->open()->count(),
            'slow_pages' => ObservabilityEvent::ofType(ObservabilityEvent::TYPE_SLOW_PAGE)->open()->count(),
            'broken_routes' => ObservabilityEvent::ofType(ObservabilityEvent::TYPE_BROKEN_ROUTE)->open()->count() + count($observability->brokenNamedRoutes()),
        ];

        return view('livewire.system-management.production-observability', [
            'events' => $events,
            'summary' => $summary,
            'types' => $this->eventTypes(),
            'severities' => $this->severities(),
            'failedJobs' => $this->failedJobs(),
            'mailFailures' => $this->mailFailures(),
            'webhookFailures' => $this->webhookFailures(),
            'slowPages' => $this->slowPages(),
            'brokenRouteHits' => $this->brokenRouteHits(),
            'brokenNamedRoutes' => $observability->brokenNamedRoutes(),
            'recentLogs' => $observability->recentLogEntries(),
        ]);
    }

    private function eventTypes(): array
    {
        return [
            ObservabilityEvent::TYPE_ERROR => 'Errors',
            ObservabilityEvent::TYPE_FAILED_JOB => 'Failed jobs',
            ObservabilityEvent::TYPE_MAIL_FAILURE => 'Mail failures',
            ObservabilityEvent::TYPE_WEBHOOK_FAILURE => 'Webhook failures',
            ObservabilityEvent::TYPE_SLOW_PAGE => 'Slow pages',
            ObservabilityEvent::TYPE_BROKEN_ROUTE => 'Broken routes',
        ];
    }

    private function severities(): array
    {
        return [
            ObservabilityEvent::SEVERITY_INFO => 'Info',
            ObservabilityEvent::SEVERITY_WARNING => 'Warning',
            ObservabilityEvent::SEVERITY_ERROR => 'Error',
            ObservabilityEvent::SEVERITY_CRITICAL => 'Critical',
        ];
    }

    private function failedJobsCount(): int
    {
        return DB::getSchemaBuilder()->hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
    }

    private function failedJobs()
    {
        if (! DB::getSchemaBuilder()->hasTable('failed_jobs')) {
            return collect();
        }

        return DB::table('failed_jobs')
            ->latest('failed_at')
            ->limit(8)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true) ?: [];

                return (object) [
                    'id' => $job->id,
                    'uuid' => $job->uuid,
                    'connection' => $job->connection,
                    'queue' => $job->queue,
                    'name' => $payload['displayName'] ?? $payload['job'] ?? 'Queued job',
                    'exception' => str($job->exception)->limit(500)->toString(),
                    'failed_at' => $job->failed_at,
                ];
            });
    }

    private function mailFailuresCount(): int
    {
        $events = ObservabilityEvent::ofType(ObservabilityEvent::TYPE_MAIL_FAILURE)->open()->count();

        if (! DB::getSchemaBuilder()->hasTable('newsletter_interactions')) {
            return $events;
        }

        return $events + NewsletterInteraction::where('type', NewsletterInteraction::TYPE_SEND)
            ->where('status', NewsletterInteraction::STATUS_FAILED)
            ->count();
    }

    private function mailFailures()
    {
        if (! DB::getSchemaBuilder()->hasTable('newsletter_interactions')) {
            return collect();
        }

        return NewsletterInteraction::query()
            ->with(['campaign:id,name,subject', 'subscriber:id,email,first_name,last_name'])
            ->where('type', NewsletterInteraction::TYPE_SEND)
            ->where('status', NewsletterInteraction::STATUS_FAILED)
            ->latest()
            ->limit(8)
            ->get();
    }

    private function webhookFailures()
    {
        $events = ObservabilityEvent::ofType(ObservabilityEvent::TYPE_WEBHOOK_FAILURE)
            ->latest('last_seen_at')
            ->limit(8)
            ->get();

        if (! DB::getSchemaBuilder()->hasTable('paystack_transactions')) {
            return $events;
        }

        $transactions = PaystackTransaction::query()
            ->whereIn('status', [PaystackTransaction::STATUS_FAILED, PaystackTransaction::STATUS_ABANDONED])
            ->latest()
            ->limit(8)
            ->get()
            ->map(function (PaystackTransaction $transaction) {
                return (object) [
                    'summary' => 'Failed Paystack transaction',
                    'source' => 'paystack',
                    'message' => $transaction->gateway_response ?: 'Transaction did not complete successfully.',
                    'reference' => $transaction->reference,
                    'last_seen_at' => $transaction->updated_at,
                    'occurrences' => 1,
                ];
            });

        return $events->concat($transactions)->sortByDesc('last_seen_at')->take(8);
    }

    private function slowPages()
    {
        return ObservabilityEvent::ofType(ObservabilityEvent::TYPE_SLOW_PAGE)
            ->latest('last_seen_at')
            ->limit(8)
            ->get();
    }

    private function brokenRouteHits()
    {
        return ObservabilityEvent::ofType(ObservabilityEvent::TYPE_BROKEN_ROUTE)
            ->latest('last_seen_at')
            ->limit(8)
            ->get();
    }
}
