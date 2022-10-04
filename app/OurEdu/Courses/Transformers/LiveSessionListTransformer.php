<?php

namespace App\OurEdu\Courses\Transformers;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;
use App\OurEdu\Courses\Transformers\CourseSessionTransformer;
use Carbon\Carbon;

use function GuzzleHttp\Psr7\str;

class LiveSessionListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'sessions',
        'instructor',
        'actions'
    ];

    protected array $availableIncludes = [
        'sessions',
        'instructor',
        'subject',
        'actions'
    ];

    protected $user;
    private $isSubscribed;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct($user = null)
    {
        $this->user = $user ?? new User;
        $this->tokenManager = app(TokenManagerInterface::class);
    }

    public function transform(LiveSession $liveSession)
    {
        $userIsSubscribed = DB::table('course_student')
            ->where('course_id', $liveSession->id)
            ->where('student_id', auth()->user()->student->id)
            ->exists();
        $this->isSubscribed = $userIsSubscribed;
        $currencyCode = $this->user->student->educationalSystem->country->currency ?? '';
        $transformedData = [
            'id' => (int) $liveSession->id,
            'name' => (string) $liveSession->name,
            'truncate_name' => (string)(truncateString($liveSession->name,10)),

            'instructor_id' => (int) $liveSession->instructor_id,
            'subject_id' => (int) $liveSession->subject_id,
            'subscription_cost' =>(float) $liveSession->subscription_cost . " " . $currencyCode,
            'is_active' => (boolean) $liveSession->is_active,
            'subscribe' => (boolean) $this->isSubscribed,
            'picture' => (string) imageProfileApi($liveSession->picture,'small')
        ];

        return $transformedData;
    }

    public function includeActions(LiveSession $liveSession)
    {
        $isSessionRunning = Carbon::now()->between(Carbon::parse($liveSession->session->date.' '.$liveSession->session->start_time)->format('Y-m-d H:i:s'), Carbon::parse($liveSession->session->date.' '.$liveSession->session->end_time)->format('Y-m-d H:i:s'));

        $actions = [];
        $authUser = Auth::guard('api')->user();
        if ( $authUser->type == UserEnums::STUDENT_TYPE && $student = $authUser->student) {
            if (! $this->isSubscribed ) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.liveSessions.subscribe', ['liveSessionId' => $liveSession->id]),
                    'label' => trans('app.Subscribe'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::LIVE_SESSION_SUBSCRIBE
                ];
            }else if($isSessionRunning) {
                //student is subscribed to session, then return join/start action
                $courseSession = $liveSession->session;
                if ($vcrSession = getVCRSessionFromCourseSessionByParticipant($courseSession, $student)) {
                    $vcrSessionId = $vcrSession->id;
                    $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

                    $url = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                        ['session_id' => $vcrSessionId, 'token' => $token,
                            'type' => VCRSessionsTypeEnum::LIVE_SESSION,
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
        }

        if(count($actions)>0){
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeInstructor(LiveSession $liveSession)
    {
        if ($liveSession->instructor) {
            return $this->item($liveSession->instructor, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeSubject(LiveSession $liveSession)
    {
        if ($liveSession->subject) {
            return $this->item($liveSession->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeSessions(LiveSession $liveSession)
    {
        return $this->collection($liveSession->sessions, new CourseSessionTransformer(), ResourceTypesEnums::COURSE_SESSION);
    }
}
