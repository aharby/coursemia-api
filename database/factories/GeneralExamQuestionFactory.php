<?php

namespace Database\Factories;

use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneralExamQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GeneralExamQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $difficultyLevel = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first() ?? Option::factory()->create(['type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL]);
        $section = create(SubjectFormatSubject::class);

        return [
            'question'    =>    $this->faker->sentence(5) . '?',
            'difficulty_level_id' => $difficultyLevel->id,
            'subject_format_subject_id' => $section->id,
            'is_true' => $this->faker->boolean(),
            'general_exam_id' => GeneralExam::factory()->create()->id,
            'question_type' => array_random(LearningResourcesEnums::getQuestionLearningResources()),
        ];
    }
}
