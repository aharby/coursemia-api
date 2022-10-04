<?php

namespace Database\Factories;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class DragDropDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DragDropData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description'    => $this->faker->words(5, true),
            'time_to_solve'    => ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            'resource_subject_format_subject_id'    =>    ResourceSubjectFormatSubject::first()->id ?? ResourceSubjectFormatSubject::factory()->create()->id,
            'drag_drop_type'    =>    Option::where('type', OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE)->first()->id ?? Option::factory()->create(['type' => OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE])->id,
        ];
    }
}
