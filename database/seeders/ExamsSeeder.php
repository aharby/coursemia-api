<?php

namespace Database\Seeders;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subject = Subject::whereHas('subjectFormatSubject')->whereHas('preparedQuestions')->latest()->first();
        $exams = Exam::factory()->count(5)->create([
            'subject_id' => $subject->id,
            'subject_format_subject_id' => json_encode($subject->subjectFormatSubject()->pluck('id')->toArray()),
            'type' => 'exam',
        ]);

        $exams->each(function ($exam) use ($subject) {
            $question = $subject->preparedQuestions()->inRandomOrder()->where(
                'difficulty_level',
                $exam->difficulty_level
            )->first();
            for ($i = 0; $i < $exam->questions_number; $i++) {
                $examQuestion = $exam->examQuestions()->create([
                    'slug' => $question->question_type,
                    'exam_id' => $exam->id,
                    'question_type' => $question->question_type,
                    'question_table_type' => $question->question_table_type,
                    'question_table_id' => $question->question_table_id,
                    'subject_id' => $subject->id,
                    'subject_format_subject_id' => $question->subject_format_subject_id,
                    'is_correct_answer' => random_int(0, 1),
                    'is_answered' => 1
                ]);

                if ($examQuestion->question_type == LearningResourcesEnums::TRUE_FALSE) {
                    $option = $examQuestion->questionable->options()->first();
                    $examQuestion->answers()->create([
                        'is_correct_answer' => $option->is_correct_answer,
                        'option_table_type' => TrueFalseOption::class,
                        'option_table_id' => $option->id
                    ]);
                }

                if ($examQuestion->question_type == LearningResourcesEnums::MULTI_CHOICE) {
                    $options = $examQuestion->questionable->options()->limit(random_int(1, 2))->get();
                    foreach ($options as $option) {
                        $examQuestion->answers()->create([
                            'is_correct_answer' => $option->is_correct_answer,
                            'option_table_type' => MultipleChoiceOption::class,
                            'option_table_id' => $option->id
                        ]);
                    }
                }

                if (in_array($examQuestion->question_type, [LearningResourcesEnums::DRAG_DROP, LearningResourcesEnums::MATCHING, LearningResourcesEnums::MULTIPLE_MATCHING])) {
                    $examQuestion->answers()->create([
                        'is_correct_answer' => random_int(0, 1),
                    ]);
                }
            }
        });

        Artisan::call('questions:report');
    }
}
