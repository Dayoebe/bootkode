<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Admin\BulkEnrollmentBatch;

class BulkEnrollmentCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $batch;
    protected $results;

    public function __construct(BulkEnrollmentBatch $batch, array $results)
    {
        $this->batch = $batch;
        $this->results = $results;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->batch->isCompleted() 
            ? 'Bulk Enrollment Completed - ' . $this->batch->name
            : 'Bulk Enrollment Failed - ' . $this->batch->name;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Bulk Enrollment Update')
            ->line('Your bulk enrollment batch "' . $this->batch->name . '" has been processed.');

        if ($this->batch->isCompleted()) {
            $mail->line('**Results:**')
                ->line('• Total processed: ' . number_format($this->results['processed']))
                ->line('• Successfully enrolled: ' . number_format($this->results['successful']))
                ->line('• Failed enrollments: ' . number_format($this->results['failed']));
                
            if ($this->results['failed'] > 0) {
                $mail->line('You can download an error report from the bulk enrollment section.');
            }
            
            $mail->action('View Details', route('institution.bulk-enrollment'));
        } else {
            $mail->line('Unfortunately, the batch processing failed. Please check the details in your dashboard or contact support if you need assistance.');
        }

        return $mail->salutation('Best regards, The ' . config('app.name') . ' Team');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Bulk Enrollment ' . ($this->batch->isCompleted() ? 'Completed' : 'Failed'),
            'message' => 'Batch "' . $this->batch->name . '" processed: ' . 
                        $this->results['successful'] . ' successful, ' . 
                        $this->results['failed'] . ' failed',
            'batch_id' => $this->batch->id,
            'institution_id' => $this->batch->institution_id,
            'results' => $this->results,
            'type' => 'bulk_enrollment_completed'
        ];
    }
}