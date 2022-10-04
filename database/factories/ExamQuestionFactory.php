<?php

namespace Database\Factories;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExamQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $question = TrueFalseQuestion::factory()->create();

        return [
            'exam_id'    =>    Exam::factory()->create(),
            'subject_id'    =>    Subject::first()->id ?? Subject::factory()->create(),
            'question_type'    =>    $this->faker->word(1),
            'question_table_type'    =>    get_class($question),
            'question_table_id'    =>    $question->id,
            'slug'    =>    str_slug($this->faker->word(1)),
        ];
    }
}
