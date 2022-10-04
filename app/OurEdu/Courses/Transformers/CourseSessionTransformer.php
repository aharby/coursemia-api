<?php

namespace App\OurEdu\Courses\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use App\OurEdu\VCRSessions\General\Transformers\RecordedVcrSessionTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Users\UserEnums;

class CourseSessionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'recordedSessions'
    ];
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    private $params;
    /**
     * @var VCRSessionUseCaseInterface
     */
    private VCRSessionUseCaseInterface $VCRSessionUseCase;
    private string $meeting_type = VCRProvidersEnum::AGORA;


    /**
     * CourseSessionTransformer constructor.
     */
    public function __construct($params = [])
    {
        $this->params = $params;
        $this->tokenManager = app(TokenManagerInterface::class);
        $this->VCRSessionUseCase = app(VCRSessionUseCaseInterface::class);
    }

    public function transform(CourseSession $courseSession)
    {
        $transformedData = [
            'id' => (int)$courseSession->id,
            'course_id' => (int)$courseSession->course_id,
            'date' => (string)$courseSession->date,
            'content' => (string)$courseSession->content,
            'start_time' => (string)date("g:iA", strtotime($courseSession->start_time)),
            'end_time' => (string)date("g:iA", strtotime($courseSession->end_time)),

        ];

        $vcrSession = $courseSession->VCRSession;

        if ($vcrSession) {
            $transformedData["vcr_session_id"] = $vcrSession->id;
            $this->meeting_type = $this->VCRSessionUseCase->getSessionMeetingProvider($vcrSession);
        }

        $transformedData["meeting_type"] = $this->meeting_type;

        return $transformedData;
    }

    public function includeActions(CourseSession $courseSession)
    {
        $actions = [];
        $student = isset($this->params['user'])? $this->params['user']->student:Auth::guard('api')->user()->student;
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.courses.courseSession', ['id' => $courseSession->id]),
            'label' => trans('app.course session'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_COURSE_SESSION
        ];

        $userIsSubscribed = DB::table('course_student')
            ->where('course_id', $courseSession->course_id)
            ->where('student_id', $student->id)
            ->exists();
           
        if ($userIsSubscribed && Auth::guard('api')->user()->type != UserEnums::PARENT_TYPE && $courseSession->date == date('Y-m-d') &&
         ( $courseSession->start_time <= date('H:i:s') &&  $courseSession->end_time >= date('H:i:s'))) {
            $sessionType = $courseSession->course->type == CourseEnums::LIVE_SESSION ? VCRSessionsTypeEnum::LIVE_SESSION :VCRSessionsTypeEnum::COURSE_SESSION;
            if ($vcrSession = getVCRSessionFromCourseSessionByParticipant($courseSession, $student)) {
                $vcrSessionId = $vcrSession->id;
                $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

                $url = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                    ['session_id' => $vcrSessionId, 'token' => $token,
                        'type' => $sessionType,
                        'portal_url' => env('VCR_PORTAL_URL','https://vcr.ta3lom.com')
                    ]);

                $actions[] = [
                    'endpoint_url' => $url,
                    'label' => trans('vcr.Start Session'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::START_SESSION
                ];
            }
        }


        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeRecordedSessions(CourseSession $courseSession)
    {
        if (isset($this->params['userIsSubscribed']) && $this->params['userIsSubscribed'])
        {
            $recordedSessions = $courseSession->VCRSession->recordedFile()->get();
            return $this->collection($recordedSessions, new RecordedVcrSessionTransformer(),ResourceTypesEnums::VCR_RECORD);
        }

    }
}
