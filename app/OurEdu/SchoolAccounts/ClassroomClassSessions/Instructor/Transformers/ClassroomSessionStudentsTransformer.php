<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers\SessionPreparationTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\SessionScoreTransformer;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;

class ClassroomSessionStudentsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
        'sessionScore'
    ];

    protected $params;
    public function __construct($params){
        $this->params = $params;
    }


    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => (int)$user->id,
            'name'=>(string)$user->first_name . ' '.$user->last_name,
            'classroom_id' => (int)$user->student->classroom_id,

            'isAttends' => (bool) $user->v_c_r_sessions_presence_count > 0 ? true : false,

            'count_of_session_viewed_media'=>(int)
                $this->params['sessionPreparationMedia']?
                    $user->preparationMedia->whereIn('id',$this->params['sessionPreparationMedia'])
                        ->whereNotNull('pivot.viewed_at')->count():0,

            'count_of_session_downloaded_media'=>(int)
                $this->params['sessionPreparationMedia']?
                    $user->preparationMedia->whereIn('id',$this->params['sessionPreparationMedia'])
                        ->whereNotNull('pivot.downloaded_at')->count():0,
        ];
    }

    private function instructorAttends($vcrSessionId)
    {
        //Checking if instructor attends this session
        return VCRSessionPresence::where('vcr_session_id', $vcrSessionId)
            ->where('user_role', UserEnums::SCHOOL_INSTRUCTOR)->exists();
    }

    public function includeActions($user){
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.classroomClasses.post-student-session-result',
            [
                'sessionId' => $this->params['session']->id,
                'studentId'=>$user->id
            ]),
            'label' => trans('classroomClassSession.Score student\'s session result'),
            'method' => 'POST',
            'key' => APIActionsEnums::SCORE_STUDENTS_SESSION_RESULT
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeSessionScore($user){
        return $this->collection($user->userSessionScores, new SessionScoreTransformer(), ResourceTypesEnums::SESSION_SCORE);
    }
}
