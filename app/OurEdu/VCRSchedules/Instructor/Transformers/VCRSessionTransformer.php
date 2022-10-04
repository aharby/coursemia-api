<?php


namespace App\OurEdu\VCRSchedules\Instructor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class VCRSessionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'subject',
        'actions',
    ];
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * VCRSessionTransformer constructor.
     */
    public function __construct()
    {
        $this->tokenManager = app(TokenManagerInterface::class);
    }

    public function transform(VCRSession $session)
    {
        $data = $session->vcrRequest->workingDay;

        return [
            'id' => $session->id,
            'session_type' => $session->vcr_session_type,
            'start_time' => $data?->from_time ??  $session->time_to_start,
            "end_time" => $data?->to_time ?? $session->time_to_end,
            "student_name" => $session->vcrRequest->student->user->name ?? null,
            "subject_name" => $session->subject?->name,
            'meeting_type' => $this->getSchoolMeetingType($session)
        ];
    }

    public function includeSubject(VCRSession $session)
    {
        $subject=  $session->subject ?? null;

        if($subject){
            return $this->item($subject, new ScheduleSubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeActions(VCRSession $session)
    {
        $actions = [];
        if ($this->checkTime( $session) && $session->status != VCRRequestStatusEnum::REJECTED) {
            $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

            $url = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                [
                    'session_id' => $session->id,
                    'token' => $token,
                    'type' => VCRSessionsTypeEnum::LIVE_SESSION,
                    'portal_url' => env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com')
                ]);

            $actions[] = [
                'endpoint_url' => $url,
                'label' => trans('vcr.Start Session'),
                'method' => 'POST',
                'key' => APIActionsEnums::START_SESSION
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    private function checkTime(VCRSession $session)
    {
        if($session->vcr_request_id){
            $data = $session->vcrRequest->workingDay;

        return  Carbon::now()->between($data->from_time, $data->to_time);
        }
        return Carbon::now()->between($session->time_to_start, $session->time_to_end);
    }
    private function getSchoolMeetingType(VCRSession $session): string
    {
        $meetingType = $session->classroom->branch->meeting_type ?? '';

        return in_array($meetingType, VCRProvidersEnum::getList()) ?
            $meetingType :
            $this->getSystemMeetingType();
    }

    private function getSystemMeetingType(): string
    {
        $configs = getConfigs();
        $systemMeetingType = $configs['meeting_type'][''] ?? '';

        return in_array($systemMeetingType, VCRProvidersEnum::getList()) ?
            $systemMeetingType :
            VCRProvidersEnum::getDefaultProvider();
    }
}
