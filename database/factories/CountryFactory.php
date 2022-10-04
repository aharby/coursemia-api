<?php

namespace Database\Factories;

use App\OurEdu\Countries\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name:en' => $this->faker->firstName,
            'name:ar' => $this->faker->firstName,
            'country_code' => $this->faker->countryCode,
            'currency_code' => $this->faker->currencyCode,
            'is_active' => $this->faker->boolean(50),
        ];
    }
}
