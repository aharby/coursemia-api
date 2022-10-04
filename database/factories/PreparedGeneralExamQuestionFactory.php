<?php

namespace Database\Factories;

use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreparedGeneralExamQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PreparedGeneralExamQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $question = CompleteQuestion::factory()->create();

        $difficultyLevel = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first() ?? Option::factory()->create(['type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL]);

        $section = SubjectFormatSubject::factory()->create();

        return [
            'question_type' => LearningResourcesEnums::COMPLETE,
            'difficulty_level_id' => $difficultyLevel->id,
            'questionable_id' => $question->id,
            'questionable_type' => get_class($question),
            'subject_id' => $section->subject_id,
            'subject_format_subject_id' => $section->id,
        ];
    }
}
