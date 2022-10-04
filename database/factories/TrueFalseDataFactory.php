<?php

namespace Database\Factories;

use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrueFalseDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrueFalseData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description'    => $this->faker->words(5, true),
            'resource_subject_format_subject_id'    =>    ResourceSubjectFormatSubject::first()->id ?? ResourceSubjectFormatSubject::factory()->create()->id,
            'true_false_type'    =>    Option::where('slug', ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT)->first()->id ?? Option::factory()->create(['slug', ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT])->id,
            'question_type'    =>    Option::where('type', 'true_false_true_false_type')->first()->id ?? Option::factory()->create(['type' => 'true_false_true_false_type'])->id
        ];
    }
}
