<?php

namespace App\OurEdu\Courses\Transformers;

use Carbon\Carbon;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;

class CourseSessionListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    private $tokenManager;

    public function __construct()
    {
        $this->tokenManager = app(TokenManagerInterface::class);
    }
    public function transform(CourseSession $courseSession)
    {
        return [
            'id' => (int) $courseSession->id,
            'course_id' => (int) $courseSession->course_id,
            'date' => (string) date("d M Y", strtotime($courseSession->date)),
            'content' => (string) $courseSession->content,
            'start_time' => (string) date("g:iA", strtotime($courseSession->start_time)),
            'end_time' => (string)date("g:iA", strtotime($courseSession->end_time)) ,
        ];

    }

    public function includeActions(CourseSession $courseSession)
    {
        $actions = [];
        $authUser = Auth::guard('api')->user();
        if (($authUser->type == UserEnums::STUDENT_TYPE && $student = $authUser->student)) {
                if ($vcrSession = getVCRSessionFromCourseSessionByParticipant($courseSession, $student)) {
                    $vcrSessionId = $vcrSession->id;
                    $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

                    $url = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                        ['session_id' => $vcrSessionId, 'token' => $token,
                            'type' => VCRSessionsTypeEnum::COURSE_SESSION,
                            'portal_url' => env('VCR_PORTAL_URL','https://vcr.ta3lom.com')
                        ]);
                    $actions[] = [
                        'endpoint_url' => $url,
                        'label' => trans('vcr.join'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::JOIN
                    ];
                }

        }

        if(count($actions)>0){
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
