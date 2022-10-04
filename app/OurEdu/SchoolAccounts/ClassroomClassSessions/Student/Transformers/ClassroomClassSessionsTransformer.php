<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\SubjectTransformer;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\VCRSchedules\Models\VCRSession;

class ClassroomClassSessionsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'beforeQuizzes',
        'afterQuizzes',
        'actions',
    ];

    protected array $availableIncludes = [
        'subject',
        'instructor',
        'classroom',
    ];
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;
    private $params;
    /**
     * ClassroomClassSessionsTransformer constructor.
     */
    public function __construct($params = [])
    {
        $this->tokenManager = app(TokenManagerInterface::class);
        $this->params = $params;
    }


    /**
     * @param ClassroomClassSession $session
     * @return array
     */
    public function transform(VCRSession $session)
    {
        $isSessionRunning = (Carbon::now(Config::get('app.timezone'))->between($session->time_to_start, $session->time_to_end)&&($session->status != VCRSessionsStatusEnum::FINISHED) && $session->status != VCRSessionsStatusEnum::FINISHED);


        $url = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
            ['session_id' => $session->id,
                'token' => $this->params['token'],
                'type' => $session->vcr_session_type,
                'portal_url' => env('VCR_PORTAL_URL','https://vcr.ta3lom.com')
            ]);
        $transformedArray = [
            'id' => (int)$session->classroom_session_id,
            'classroom_id' => (int)$session->classroom_id,
            'subject_id' => (int)$session->subject_id,
            'day' => $session->classroomClassSession->from->format('Y-m-d'),
            'from_time' => $session->classroomClassSession->from->format("H:i"),
            'to_time' => $session->classroomClassSession->to->format("H:i"),
            'is_session_running' => $isSessionRunning ,
            'join_url' => $url,
            'is_ended_by_instructor' => $session->is_ended_by_instructor,
            'is_ended_by_instructor_message' =>  trans('classroomClassSession.Ended by Instructor') ,
            'status' => $isSessionRunning ? trans('classroomClassSession.running') : trans('classroomClassSession.not started yet'),
            'vcr_session_id' => $session->id ?? '',
            'meeting_type' => $session->meeting_type,
            'token' => $this->params['token'],
            'local' => app()->getLocale(),
            'baseUrl' => env('APP_URL') ? env('APP_URL').'/api/v1' : 'https://admin.ta3lom.com/api/v1',
            'sessionType' => 'school_session'
        ];

        if ($session->is_ended_by_instructor || $session->time_to_end < Carbon::now()) {
            $transformedArray['status'] = trans('classroomClassSession.Ended');
        }

        $joinUrls = getStudentSessionUrls($session->id, $session->vcr_session_type,$this->params['token']);
        return array_merge($transformedArray, $joinUrls);
    }

    public function includeSubject($session)
    {

        if ($session->subject) {
            return $this->item($session->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeClassroom($session)
    {

        if ($session->classroom) {
            return $this->item($session->classroom, new ClassroomTransformer(), ResourceTypesEnums::CLASSROOM);
        }
    }

    public function includeInstructor($session)
    {
        if ($session->instructor) {
            return $this->item($session->instructor, new InstructorTransformer(), ResourceTypesEnums::INSTRUCTOR);
        }
    }

    public function includeBeforeQuizzes($session)
    {
        $beforeSessionQuizzes = $session->beforeSessionQuizzes;

        if (count($beforeSessionQuizzes)) {
            return $this->collection($beforeSessionQuizzes, new QuizTransformer(), ResourceTypesEnums::BEFORE_QUIZ);
        }
    }

    public function includeAfterQuizzes($session)
    {
        $afterSessionQuizzes = $session->afterSessionQuizzes;

        if (count($afterSessionQuizzes)) {
            return $this->collection($afterSessionQuizzes, new QuizTransformer(), ResourceTypesEnums::AFTER_QUIZ);
        }
    }

    public function includeActions($session)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.online-sessions.getVCRSession', ['sessionId' => $session->id, 'type' => $session->vcr_session_type]),
            'label' => trans('app.Subscribe'),
            'method' => 'POST',
            'key' => APIActionsEnums::JOIN_LIVE_SESSION
        ];
        if (count($actions)){
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }

    }
}
