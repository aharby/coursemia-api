<?php

namespace Database\Factories;

use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionReportTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuestionReportTask::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $question = CompleteQuestion::factory()->create();

        $title = $this->faker->word(3);

        return [
            'title' => $title,
            'slug' => str_slug($title),
            'is_active' => 1,
            'is_expired' => $this->faker->boolean(),
            'is_done' => $this->faker->boolean(),
            'is_assigned' => $this->faker->boolean(),
            'subject_id' => Subject::first()->id ?? Subject::factory()->create()->id,
            'question_type'    =>    get_class($question),
            'question_id'    =>    $question->id,
        ];
    }
}
