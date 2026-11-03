<?php

declare(strict_types=1);

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\LessonProgress;
use App\Models\Learning\SavedResource;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$database = (string) DB::connection()->getDatabaseName();
if (! str_ends_with($database, '_test') && ! str_ends_with($database, '_testing')) {
    throw new RuntimeException("Refusing to seed browser fixtures in non-test database: {$database}");
}

$output = getenv('BROWSER_JOURNEY_FIXTURES');
if (! is_string($output) || $output === '') {
    throw new RuntimeException('BROWSER_JOURNEY_FIXTURES is required.');
}

$fixtures = DB::transaction(function (): array {
    $learner = User::where('email', 'learner@example.test')->firstOrFail();
    $course = Course::where('slug', 'reliable-offline-practice')->firstOrFail();
    $lessons = $course->sections()->orderBy('order')->with(['lessons' => fn ($query) => $query->orderBy('order')])->get()->flatMap->lessons->values();
    $firstLesson = $lessons->firstOrFail();

    $assessment = Assessment::withTrashed()->where('lesson_id', $firstLesson->id)->where('title', 'Browser journey checkpoint')->first();
    if ($assessment?->trashed()) {
        $assessment->restore();
    }
    $assessment ??= new Assessment();
    $assessment->fill([
        'course_id' => $course->id,
        'lesson_id' => $firstLesson->id,
        'title' => 'Browser journey checkpoint',
        'description' => 'Synthetic test-database checkpoint for the complete learner journey.',
        'type' => 'quiz',
        'pass_percentage' => 70,
        'estimated_duration_minutes' => 5,
        'max_attempts' => 3,
        'order' => 1,
    ]);
    $assessment->save();

    $question = Question::withTrashed()->where('assessment_id', $assessment->id)->where('question_text', 'Which boundary protects a learner action?')->first();
    if ($question?->trashed()) {
        $question->restore();
    }
    $question ??= new Question();
    $question->fill([
        'assessment_id' => $assessment->id,
        'question_text' => 'Which boundary protects a learner action?',
        'question_type' => 'multiple_choice',
        'options' => ['Only hiding the button', 'Server authorization and enrollment checks'],
        'correct_answers' => [1],
        'explanation' => 'The server must recheck authorization and enrollment.',
        'points' => 1,
        'order' => 1,
    ]);
    $question->save();

    $lessonIds = $lessons->pluck('id')->map(fn ($id) => (int) $id)->all();
    StudentAnswer::where('user_id', $learner->id)->where('assessment_id', $assessment->id)->delete();
    LearningSession::where('user_id', $learner->id)->where('activity_type', 'assessment')->get()
        ->filter(fn (LearningSession $session) => (int) ($session->metadata['assessment_id'] ?? 0) === (int) $assessment->id)
        ->each->delete();
    DB::table('lesson_user')->where('user_id', $learner->id)->whereIn('lesson_id', $lessonIds)->delete();
    LessonProgress::where('user_id', $learner->id)->whereIn('lesson_id', $lessonIds)->delete();
    SavedResource::where('user_id', $learner->id)->where('course_id', $course->id)->delete();
    Certificate::where('user_id', $learner->id)->where('course_id', $course->id)->delete();
    CourseEnrollment::where('user_id', $learner->id)->where('course_id', $course->id)->update([
        'progress_percentage' => 0,
        'is_completed' => false,
        'completed_at' => null,
    ]);
    DB::table('course_user')->where('user_id', $learner->id)->where('course_id', $course->id)->update([
        'progress' => 0,
        'completed_at' => null,
        'last_accessed_at' => null,
        'updated_at' => now(),
    ]);

    return [
        'email' => $learner->email,
        'course' => route('course.view', $course->slug, false),
        'course_title' => $course->title,
        'lesson_ids' => $lessonIds,
        'assessment_id' => $assessment->id,
        'correct_answer' => 'Server authorization and enrollment checks',
        'certificates' => route('student.certificates.index', absolute: false),
    ];
});

if (file_put_contents($output, json_encode($fixtures, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)) === false) {
    throw new RuntimeException("Unable to write {$output}");
}

fwrite(STDOUT, "Seeded complete journey fixture in {$database}.\n");
