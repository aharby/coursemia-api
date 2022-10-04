<?php

namespace Database\Factories;

use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneralExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GeneralExam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $now = now()->addWeek();
        $subject = Subject::factory()->create();
        $difficultyLevel = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first() ?? create(Option::class)->create(['type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL]);
        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        return [
            'name'  =>  $this->faker->name(),
            'date'  =>  $now->format('Y-m-d'),
            'start_time'    =>  $now->format('H:i:s'),
            'end_time'  =>  $now->addHours(3)->format('H:i:s'),
            'subject_id'    =>  $subject->id,
            'subject_format_subjects'    =>  json_encode($sections->pluck('id')->toArray()),
            'difficulty_level_id'   =>  $difficultyLevel->id
        ];
    }
}
