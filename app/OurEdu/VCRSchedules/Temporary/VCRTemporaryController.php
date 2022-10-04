<?php

namespace App\OurEdu\VCRSchedules\Temporary;

use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRSessionTransformer;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRScheduleTransformer;
use Carbon\Carbon;

class VCRTemporaryController extends BaseApiController
{
    public function __construct(
        ParserInterface $parserInterface
    ) {
        $this->parserInterface = $parserInterface;

        $this->middleware('auth:api');
        $this->middleware('type:student');
        $this->user = Auth::guard('api')->user();
    }

    public function listAvailableInstructors()
    {
        $availableSubjectsForStudent = auth()->user()->student->subjects->pluck('id')->toArray();

        $VCRSchedules = VCRSchedule::whereIn('subject_id' , $availableSubjectsForStudent)
            ->whereDate('from_date', '<=', now()->format('Y-m-d'))
            ->whereDate('to_date', '>=', now()->format('Y-m-d'))
            ->with('instructor.instructor', 'subject')
             ->wherehas('workingDays' , function($q){
                $q->whereTime('from_time','<=', Carbon::now()->format('H:i:s'))
                  ->whereTime('to_time','>=', Carbon::now()->format('H:i:s'))
                ->where('day', Carbon::now()->format('l'));
            })
            ->paginate();

            return $this->transformDataModInclude(
            $VCRSchedules,
            ['instructor.user', 'subject', 'actions'],
            new VCRScheduleTransformer(),
            ResourceTypesEnums::VCR_SCHEDULE
        );
    }

    public function requestVCRSession(\Illuminate\Http\Request $request, VCRSchedule $vcrSchedule)
    {
        $vcrRequest = VCRRequest::create([
            'student_id' => $this->user->student->id,
            'instructor_id' => $vcrSchedule->instructor_id,
            'subject_id' => $vcrSchedule->subject_id,
            'vcr_schedule_id' => $vcrSchedule->id,
            'vcr_day_id' => $vcrSchedule->workingDays()->first()->id,
            'price' => $vcrSchedule->price,
            'status' => VCRRequestStatusEnum::ACCEPTED
        ]);


        $sessionData = [
            'student_id' => $vcrRequest->student_id,
            'instructor_id' => $vcrRequest->instructor_id,
            'subject_id' => $vcrRequest->subject_id,
            'vcr_request_id' => $vcrRequest->id,
            'price' => $vcrRequest->price,
            'student_join_url'  =>  $bbbMeeting['attende_url'],
            'instructor_join_url'  =>  $bbbMeeting['moderator_url'],
            'status' => VCRSessionsStatusEnum::ACCEPTED
        ];

        $vcrSession = VCRSession::create($sessionData);

        return $this->transformDataModInclude($vcrSession, ['ratings.user', 'ratings.instructor'], new VCRSessionTransformer(), ResourceTypesEnums::VCR_SESSION);
    }
}
