<?php

namespace Database\Factories;

use App\OurEdu\Config\Config;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Config::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        foreach (config("translatable.locales") as $lang) {
            $record[$lang] = $this->faker->sentence(3);
        }

        return [
            'type' => 'Basic Information',
            'field' => 'field_' . str_random(10),
            'en'  => ['label' => $this->faker->colorName, 'value' => $this->faker->hexColor],
            'ar'  => ['label' => $this->faker->colorName, 'value' => $this->faker->hexColor],
            'created_by' => 2,
        ];
    }
}
