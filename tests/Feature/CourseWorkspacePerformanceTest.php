<?php

namespace Tests\Feature;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseWorkspacePerformanceTest extends TestCase
{
    use RefreshDatabase;

    private function learner(): User
    {
        Role::findOrCreate(User::ROLE_STUDENT, 'web');
        $learner = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $learner->assignRole(User::ROLE_STUDENT);

        return $learner;
    }

    private function graph(User $learner, string $slug, int $sections, int $lessonsPerSection, int $currentAssessments): Course
    {
        $course = Course::withoutEvents(fn () => Course::create([
            'instructor_id' => $learner->id,
            'title' => 'Performance '.$slug,
            'slug' => $slug,
            'is_published' => true,
            'is_approved' => true,
        ]));

        for ($sectionNumber = 1; $sectionNumber <= $sections; $sectionNumber++) {
            $section = Section::create([
                'course_id' => $course->id,
                'title' => "Section {$sectionNumber}",
                'order' => $sectionNumber,
            ]);

            for ($lessonNumber = 1; $lessonNumber <= $lessonsPerSection; $lessonNumber++) {
                $lesson = Lesson::withoutEvents(fn () => Lesson::create([
                    'section_id' => $section->id,
                    'title' => "Lesson {$sectionNumber}.{$lessonNumber}",
                    'slug' => "{$slug}-{$sectionNumber}-{$lessonNumber}",
                    'order' => $lessonNumber,
                    'published_at' => now(),
                ]));

                if ($sectionNumber === 1 && $lessonNumber === 1) {
                    for ($assessmentNumber = 1; $assessmentNumber <= $currentAssessments; $assessmentNumber++) {
                        $assessment = Assessment::create([
                            'course_id' => $course->id,
                            'lesson_id' => $lesson->id,
                            'title' => "Assessment {$assessmentNumber}",
                            'type' => 'quiz',
                            'pass_percentage' => 70,
                            'order' => $assessmentNumber,
                        ]);
                        Question::create([
                            'assessment_id' => $assessment->id,
                            'question_text' => "Question {$assessmentNumber}",
                            'question_type' => 'multiple_choice',
                            'options' => ['A', 'B'],
                            'correct_answers' => [0],
                            'points' => 1,
                            'order' => 1,
                        ]);
                    }
                }
            }
        }

        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $learner->id,
            'enrolled_at' => now(),
            'progress_percentage' => 0,
            'is_completed' => false,
        ]);

        return $course;
    }

    public function test_dense_course_workspace_keeps_a_bounded_query_graph(): void
    {
        $learner = $this->learner();
        $small = $this->graph($learner, 'small-workspace', 1, 1, 1);
        $dense = $this->graph($learner, 'dense-workspace', 8, 5, 12);
        $this->actingAs($learner);

        $activeBucket = null;
        $queries = ['small' => [], 'dense' => []];
        DB::listen(function ($query) use (&$activeBucket, &$queries): void {
            if ($activeBucket) {
                $queries[$activeBucket][] = $query->sql;
            }
        });

        $activeBucket = 'small';
        $this->get(route('course.view', $small->slug))->assertOk();
        $activeBucket = 'dense';
        $this->get(route('course.view', $dense->slug))->assertOk();
        $activeBucket = null;

        $this->assertLessThanOrEqual(
            count($queries['small']) + 4,
            count($queries['dense']),
            'Course workspace queries must not grow with sections, lessons, assessments, or questions.'
        );
        $this->assertLessThanOrEqual(60, count($queries['dense']));
    }
}
