<?php

namespace Database\Factories;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class VCRRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VCRRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $exam = Exam::factory()->create();

        return [
            'student_id' => $exam->student_id,
            'instructor_id' => Instructor::factory()->create()->id,
            'subject_id' => $exam->subject_id,
            'vcr_schedule_id' => VCRSchedule::factory()->create()->id,
            'vcr_day_id' => VCRScheduleDays::factory()->create()->id,
            'accepted_at' => now(),
            'exam_id' => $exam->id,
            'price' => 10,
            'status' => VCRRequestStatusEnum::ACCEPTED,
        ];
    }
}
