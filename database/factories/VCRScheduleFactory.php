<?php

namespace Database\Factories;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class VCRScheduleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VCRSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject_id' => Subject::first() ? Subject::first()->id : Subject::factory()->create()->id,
            'instructor_id' => Instructor::first() ? Instructor::first()->id : null,
            'from_date' => date('Y.m.d'),
            'to_date' => date('Y.m.d'),
            'price' => 100,
            'is_active' => 1,
        ];
    }
}
