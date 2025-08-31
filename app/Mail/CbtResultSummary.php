<?php
// App/Mail/CbtResultSummary.php
namespace App\Mail;

use App\Models\CbtResult;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CbtResultSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $cbtResult;

    public function __construct(CbtResult $cbtResult)
    {
        $this->cbtResult = $cbtResult;
    }

    public function build()
    {
        return $this->markdown('emails.cbt.result-summary')
            ->subject("CBT Results: {$this->cbtResult->exam->title}")
            ->with([
                'result' => $this->cbtResult,
                'exam' => $this->cbtResult->exam,
                'user' => $this->cbtResult->user,
            ]);
    }
}