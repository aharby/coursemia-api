<?php

namespace App\OurEdu\VCRSchedules\Observers;

use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Illuminate\Support\Str;

class VCRScheduleDaysObserver
{

    /**
     * Handle the User "created" event.
     *
     * @param  VCRScheduleDays  $schedule
     * @return void
     */
    public function created(VCRScheduleDays $vCRScheduleDay)
    {
        $schedule = $vCRScheduleDay->vcrSchedule;

        $dayRepetitions = dayRepeated($vCRScheduleDay->day, $schedule->from_date, $schedule->to_date);

        foreach ($dayRepetitions as $day) {
            VCRSession::create([
                'instructor_id' => $schedule->instructor_id,
                'subject_id' => $schedule->subject_id,
                'vcr_schedule_day_id' => $vCRScheduleDay->id,
                'vcr_schedule_id' => $vCRScheduleDay->vcr_schedule_instructor_id,
                'vcr_session_type' => VCRSessionEnum::VCR_SCHEDULE_SESSION,
                'room_uuid' => substr(Str::uuid(), 0, 30),
                'agora_instructor_uuid' => Str::uuid(),
                'time_to_start' => date('Y-m-d H:i:s', strtotime("{$day} {$vCRScheduleDay->from_time}")),
                'time_to_end' => date('Y-m-d H:i:s', strtotime("{$day} {$vCRScheduleDay->to_time}")),
            ]);
        }
    }
}
