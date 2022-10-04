<?php

namespace Tests\Feature\VCRRequest\Student\Api;

use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VCRRequestsControllerTest extends TestCase
{
    use WithFaker;
    public function test_post_request_vcr()
    {
        dump('test_post_request_vcr');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $vcrSchedule = factory(VCRSchedule::class)->create();

        $student->student->update(['wallet_amount' => $vcrSchedule->price]);

        $vcrScheduleDay = factory(VCRScheduleDays::class)->create([
            'vcr_schedule_instructor_id' => $vcrSchedule->id
        ]);
        $response = $this->postJson("/api/v1/en/student/vcr/". $vcrSchedule->id ."/request/". $vcrScheduleDay->day ."/", []);
        $response->assertOk();
    }

}

