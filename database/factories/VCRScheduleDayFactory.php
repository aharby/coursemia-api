<?php

namespace Database\Factories;

use App\OurEdu\VCRSchedules\DayEnums;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use Illuminate\Database\Eloquent\Factories\Factory;

class VCRScheduleDayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VCRScheduleDays::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'day' => DayEnums::weekDays()[strtolower($this->faker->dayOfWeek())],
            'vcr_schedule_instructor_id' => VCRSchedule::first(),
            'from_time' => '00:00:00',
            'to_time' => '23:59:59',
        ];
    }
}
