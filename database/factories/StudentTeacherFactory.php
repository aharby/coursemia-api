<?php

namespace Database\Factories;

use App\OurEdu\Users\Models\StudentTeacher;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentTeacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentTeacher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'    =>    User::factory()->create(['type' => UserEnums::STUDENT_TEACHER_TYPE])->id
        ];
    }
}
