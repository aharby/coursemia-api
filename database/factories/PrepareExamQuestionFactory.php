<?php

namespace Database\Factories;

use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrepareExamQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrepareExamQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'difficulty_level' => 'difficult',
            'question_type' => 'true_false',
            'question_table_type' => 'App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion',
            'question_table_id' => rand(1, TrueFalseQuestion::max('id')),
            'question_type' => 'true_false',
            'subject_id' => Subject::where('is_aptitude', true)->first()->id,
        ];
    }
}
