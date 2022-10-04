<?php

namespace App\OurEdu\Courses\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Instructor\Transformers\ScheduleSubjectTransformer;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use App\OurEdu\VCRSessions\General\Transformers\RecordedVcrSessionTransformer;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Courses\Models\SubModels\CourseSession;

class CourseSessionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];

    protected array $availableIncludes = [
        'course',
        'subject',
        'recordedSessions'
    ];
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var VCRSessionUseCaseInterface
     */
    private VCRSessionUseCaseInterface $VCRSessionUseCase;
    private string $meeting_type = VCRProvidersEnum::AGORA;


    /**
     * CourseSessionTransformer constructor.
     */
    public function __construct()
    {
        $this->tokenManager = app(TokenManagerInterface::class);
        $this->VCRSessionUseCase = app(VCRSessionUseCaseInterface::class);
    }

    public function transform(CourseSession $courseSession)
    {
        $transformedData = [
            'id' => (int)$courseSession->id,
            'course_id' => (int)$courseSession->course_id,
            'date' => (string)date("d M Y", strtotime($courseSession->date)),
            'content' => (string)$courseSession->content,
            'start_time' => (string)date("g:iA", strtotime($courseSession->start_time)),
            'end_time' => (string)date("g:iA", strtotime($courseSession->end_time)),
            'picture' => (string)imageProfileApi($courseSession->course?->picture, 'large'),
            'medium_picture' => (string)imageProfileApi($courseSession->course?->medium_picture, 'large'),
            'small_picture' => (string)imageProfileApi($courseSession->course?->small_picture, 'large'),
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
        $instructor = Auth::guard('api')->user()->instructor;

        if ($courseSession->date == date('Y-m-d') &&
            ($courseSession->start_time <= date('H:i:s') && $courseSession->end_time >= date('H:i:s'))) {
            $sessionType = $courseSession->course->type == CourseEnums::LIVE_SESSION ? VCRSessionsTypeEnum::LIVE_SESSION : VCRSessionsTypeEnum::COURSE_SESSION;
            $vcrSession = $courseSession->vcrSession()->where('instructor_id', "=", Auth::id())->first();
            if ($vcrSession) {
                $vcrSessionId = $vcrSession->id;
                $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

                $url = getDynamicLink(
                    DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                    [
                        'session_id' => $vcrSessionId,
                        'token' => $token,
                        'type' => $sessionType,
                        'portal_url' => env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com')
                    ]
                );

                $actions[] = [
                    'endpoint_url' => $url,
                    'label' => trans('vcr.Start Session'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::START_SESSION
                ];
            }
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeCourse(CourseSession $courseSession)
    {
        if ($courseSession->course) {
            return $this->item($courseSession->course, new CourseTransformer(), ResourceTypesEnums::COURSE);
        }
    }

    public function includeSubject(CourseSession $courseSession)
    {
        if ($courseSession->course && $courseSession->course->subject) {
            return $this->item(
                $courseSession->course->subject,
                new ScheduleSubjectTransformer(),
                ResourceTypesEnums::SUBJECT
            );
        }
    }

    public function includeRecordedSessions(CourseSession $courseSession)
    {
            $recordedSessions = $courseSession->VCRSession?->recordedFile ?? [];
            return $this->collection(
                $recordedSessions,
                new RecordedVcrSessionTransformer(),
                ResourceTypesEnums::VCR_RECORD
            );
    }
}
