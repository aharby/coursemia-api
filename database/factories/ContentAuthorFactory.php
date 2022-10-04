<?php

namespace Database\Factories;

use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentAuthorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContentAuthor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'    =>    User::factory()->create(['type' => UserEnums::CONTENT_AUTHOR_TYPE])->id,
            'hire_date'    =>    $this->faker->date()
        ];
    }
}
