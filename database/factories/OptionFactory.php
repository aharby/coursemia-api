<?php

namespace Database\Factories;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Option::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' =>  $this->faker->randomElement(OptionsTypes::getOptionsTypes()),
            'title:en' =>  'Option ' . $this->faker->words(2, true),
            'title:ar' =>  'Option ' . $this->faker->words(2, true),
            'is_active' => 1,
            'created_by' => 2
        ];
    }
}
