<?php

namespace App\OurEdu\VCRSchedules\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Carbon\CarbonImmutable;

class VCRScheduleInstructorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'subject',
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    public function __construct()
    {
    }

    public function transform(VCRScheduleDays $VCRScheduleDay)
    {
        $VCRSchedules = $VCRScheduleDay->vcrSchedule;
        $transformerData = [
            'id' => $VCRScheduleDay->id,
            'day' => $VCRScheduleDay->day,
            'date' => app(CarbonImmutable::class)->next($VCRScheduleDay->day)->toDateString(),
            'from_time' => date("g:i A", strtotime($VCRScheduleDay->from_time)),
            'to_time' => date("g:i A", strtotime($VCRScheduleDay->to_time)),
            'from_date' => $VCRSchedules->from_date,
            'to_date' => $VCRSchedules->to_date,
            'VCR_sessions_number' => $VCRSchedules->VCRSessions()->count(),
        ];

        if ($VCRSchedules->subject) {
            $transformerData["Educational System"] = $VCRSchedules->subject->educationalSystem->name ?? "";
            $transformerData["Educational Term"] = $VCRSchedules->subject->educationalTerm->title ?? "";
            $transformerData["Educational Grade"] = $VCRSchedules->subject->gradeClass->title ?? "";
        }

        if (isset($VCRSchedules->working_days_count)) {
            $transformerData['working_days_count'] = $VCRSchedules->working_days_count;
        }


        return $transformerData;
    }


    public function includeSubject(VCRScheduleDays $VCRScheduleDay)
    {
        $subject=  $VCRScheduleDay->vcrSchedule->subject ??null;
        if($subject){
            return $this->item($subject, new ScheduleSubjectTransformer(), ResourceTypesEnums::SUBJECT);

        }

    }

    public function includeActions(VCRScheduleDays $VCRScheduleDay)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.vcr.getVcrSchedules.sessions', ["schedule" => $VCRScheduleDay->vcrSchedule->id]),
            'label' => trans('vcr_schedule.VCR Schedule Session'),
            'method' => 'GET',
            'key' => APIActionsEnums::VCR_SCHEDULE_SESSIONS
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

}
