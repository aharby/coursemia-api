<?php

namespace Database\Factories;

use App\OurEdu\Countries\Country;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'language' => "en",
            'email' => $this->faker->unique()->safeEmail,
            'mobile' => '0122' . rand(1111111, 9999999),
            'password' => '12345678',
            'country_id' => Country::first()->id ?? Country::factory()->create()->id,
            'confirmed' => 1,
            'is_active' => 1,
            'created_by' => 2,
            'suspended_at' => null,
            'type'  => $this->faker->randomElement(UserEnums::availableUserType()),
        ];
    }
}
