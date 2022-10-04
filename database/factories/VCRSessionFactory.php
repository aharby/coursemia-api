<?php

namespace Database\Factories;

use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class VCRSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VCRSession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $request = VCRRequest::factory()->create();

        return [
            'student_id' => $request->student_id,
            'instructor_id' => $request->instructor_id,
            'subject_id' => $request->subject_id,
            'vcr_request_id' => $request->id,
            'price' => $request->price,
            'status' => VCRSessionsStatusEnum::ACCEPTED,
            'ended_at'    =>    now()
        ];
    }
}
