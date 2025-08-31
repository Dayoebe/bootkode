<?php
// App/Jobs/ProcessCbtResults.php
namespace App\Jobs;

use App\Models\CbtResult;
use App\Models\UserAchievement;
use App\Notifications\CbtResultNotification;
use App\Notifications\CbtAchievementUnlocked;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCbtResults implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cbtResult;

    public function __construct(CbtResult $cbtResult)
    {
        $this->cbtResult = $cbtResult;
    }

    public function handle()
    {
        // Send result notification if enabled
        if ($this->cbtResult->exam->email_results) {
            $this->cbtResult->user->notify(new CbtResultNotification($this->cbtResult));
        }

        // Check and award achievements
        $newAchievements = UserAchievement::checkAndAwardAchievements($this->cbtResult->user_id);
        
        // Send achievement notifications
        foreach ($newAchievements as $achievement) {
            $this->cbtResult->user->notify(new CbtAchievementUnlocked($achievement));
        }

        // Update exam statistics
        $this->updateExamStatistics();
        
        // Generate certificate if eligible
        if ($this->cbtResult->passed && $this->cbtResult->exam->exam_type === 'certification') {
            $this->generateCertificate();
        }
    }

    private function updateExamStatistics()
    {
        $exam = $this->cbtResult->exam;
        
        // Update average score
        $avgScore = $exam->results()
            ->where('status', 'completed')
            ->avg('percentage_score');
            
        $exam->update(['average_score' => $avgScore]);
    }

    private function generateCertificate()
    {
        // This would integrate with your certificate system
        // For now, just mark as eligible
        $this->cbtResult->update(['certificate_eligible' => true]);
    }
}
