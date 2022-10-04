<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Observers;


use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClassroomClassSessionObserver
{


    public function created(ClassroomClassSession $classroomClassSession) {
            $roomUuid = substr(Str::uuid(),0,30);
        $VCRsession = VCRSession::create([
                'vcr_session_type' => VCRSessionEnum::SCHOOL_SESSION ,
                'classroom_id' => $classroomClassSession->classroom_id,
                'classroom_session_id' => $classroomClassSession->id,
                'instructor_id' => $classroomClassSession->instructor_id,
                'subject_id' => $classroomClassSession->subject_id,
                'time_to_start' => $classroomClassSession->from ,
                'time_to_end' => $classroomClassSession->to ,
                'status' => VCRRequestStatusEnum::ACCEPTED,
                'room_uuid' => $roomUuid,
                'agora_student_uuid' => Str::uuid(),
                'agora_instructor_uuid' => Str::uuid(),
                'subject_name' => $classroomClassSession->subject ? $classroomClassSession->subject->name : null
            ]);

    }

    public function deleted(ClassroomClassSession $classroomClassSession) {

        VCRSession::where('classroom_session_id' , $classroomClassSession->id)->delete();
    }
}
