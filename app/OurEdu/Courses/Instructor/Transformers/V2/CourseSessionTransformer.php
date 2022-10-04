<?php

namespace App\OurEdu\Courses\Instructor\Transformers\V2;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use Carbon\Carbon;

class CourseSessionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];

    protected array $availableIncludes = [];

    private $token;
    /**
     * CourseSessionTransformer constructor.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function transform(CourseSession $courseSession)
    {
        $transformedData = [
            'id' => (int) $courseSession->id,
            'course_id' => (int) $courseSession->course_id,
            'date' => (string) date("d M Y", strtotime($courseSession->date)),
            'content' => (string) $courseSession->content,
            'start_time' => (string) date("g:iA", strtotime($courseSession->start_time)),
            'end_time' => (string)date("g:iA", strtotime($courseSession->end_time)),

        ];

        return $transformedData;
    }

    public function includeActions(CourseSession $courseSession)
    {
        $actions = [];

        $isSessionRunning = Carbon::now()->between(
            Carbon::parse($courseSession->date . ' ' . $courseSession->start_time)->format('Y-m-d H:i:s'),
            Carbon::parse($courseSession->date . ' ' . $courseSession->end_time)->format('Y-m-d H:i:s')
        );

        if ($isSessionRunning) {
            $sessionType = $courseSession->course->type == CourseEnums::LIVE_SESSION ?
                VCRSessionsTypeEnum::LIVE_SESSION : VCRSessionsTypeEnum::COURSE_SESSION;

            $vcrSession = $courseSession->vcrSession()->where('instructor_id', "=", Auth::id())->first();

            if ($vcrSession) {
                $vcrSessionId = $vcrSession->id;

                $url = getDynamicLink(
                    DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                    [
                        'session_id' => $vcrSessionId,
                        'token' => $this->token,
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
}
