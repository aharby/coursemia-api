<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;

class ClassSessionsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'beforeQuizzes',
        'afterQuizzes',
        'subject',
    ];
    protected array $availableIncludes = [
        'preparation',
        'actions'
    ];


    /**
     * @param ClassroomClassSession $session
     * @return array
     */
    public function transform(ClassroomClassSession $session)
    {
        return [
            'id' => (int)$session->id,
            'classroom_id' => (int)$session->classroom_id,
            'subject_id' => (int)$session->subject_id,
            "day" => $session->from->format('Y-m-d'),
            "from_time" => $session->from->format("H:i"),
            "to_time" => $session->to->format("H:i"),
            "count_of_media" =>
                $session->preparation && $session->preparation->media
                ?$session->preparation->media->count():0,
            'vcrSession_id' => $session->vcrSession->id,
            'show_record' => $session->vcrSession->show_record
        ];
    }

    public function includePreparation(ClassroomClassSession $classroomClassSession)
    {
        $preparation = $classroomClassSession->preparation()->first();
        if ($preparation){
            return $this->item($classroomClassSession->preparation()->first(), new SessionPreparationTransformer(), ResourceTypesEnums::PREPARATION);
        }
    }

    public function includeBeforeQuizzes(ClassroomClassSession $session)
    {

        $beforeSessionQuizzes = $session->quizzes()
            ->where("quiz_time", "=", QuizTimesEnum::PRE_SESSION)
            ->where("quiz_type", "=", QuizTypesEnum::QUIZ)
            ->get();

        if (count($beforeSessionQuizzes)) {
            return $this->collection($beforeSessionQuizzes, new QuizTransformer(), ResourceTypesEnums::BEFORE_QUIZ);
        }
    }

    public function includeActions(ClassroomClassSession $session){
        if($session->to < now() && $session->vcrSession->status == VCRSessionsStatusEnum::FINISHED){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.classroomClasses.get-session-students',
                [
                    'sessionId' => $session->id,
                ]),
                'label' => trans('classroomClassSession.view session students'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SESSION_STUDENTS
            ];
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
    public function includeAfterQuizzes(ClassroomClassSession $session)
    {
        $afterSessionQuizzes = $session->quizzes()
            ->where("quiz_time", "=", QuizTimesEnum::AFTER_SESSION)
            ->where("quiz_type", "=", QuizTypesEnum::QUIZ)
            ->get();

        if (count($afterSessionQuizzes)) {
            return $this->collection($afterSessionQuizzes, new QuizTransformer(), ResourceTypesEnums::AFTER_QUIZ);
        }
    }

    public function includeSubject(ClassroomClassSession $session)
    {
        return $this->item($session->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);

    }
}
