<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\EducationalSupervisor\Transformers;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\SubjectTransformer;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ClassroomClassSessionsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'subject',
        'instructor',
        'classroom'
    ];
    private $params;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * ClassroomClassSessionsTransformer constructor.
     * @param TokenManagerInterface $tokenManager
     */
    public function __construct($params = [])
    {
        $this->params = $params;
        $this->tokenManager = app(TokenManagerInterface::class);
    }


    /**
     * @param VCRSession $session
     * @return array
     */
    public function transform(VCRSession $session)
    {

        $isSessionRunning = Carbon::now()->between($session->time_to_start, $session->time_to_end);

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
            "day" => $session->classroomClassSession->from->format('Y-m-d'),
            "from_time" => $session->classroomClassSession->from->format("H:i"),
            "to_time" => $session->classroomClassSession->to->format("H:i"),
            "is_session_running" => $isSessionRunning,
            "join_url" => $url,
            "vcr_session_id" => $session->id,
            "meeting_type" =>  $session->meeting_type,

        ];
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
}
