<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizStatusEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Subject\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\Subject\Transformers\SubjectTransformer;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ListPeriodicTestTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'subject',
        'sections',
        'actions',
    ];

    /**
     * @var array
     */
    private $params;
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    private $student;

    /**
     * ListPeriodicTestTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->student = Auth::guard("api")->user();
    }

    public function transform(GeneralQuiz $periodicTest)
    {
        return [
            'id' => (int)$periodicTest->id,
            'title' => (string)$periodicTest->title,
            'start_at' => (string)$periodicTest->start_at,
            'end_at' => (string)$periodicTest->end_at,
            'status' => (string)$this->getStatus($periodicTest),
        ];
    }

    private function getStatus(GeneralQuiz $periodicTest)
    {
        if ($studentPeriodicTest = $this->student->schoolStudentGeneralQuizzes()
            ->where('general_quiz_id', $periodicTest->id)
            ->first()
        ) {
            if (!$studentPeriodicTest->is_finished && is_null($studentPeriodicTest->finished_time)) {
                return GeneralQuizStatusEnum::STARTED;
            }
            return GeneralQuizStatusEnum::FINISHED;
        } else {
            return GeneralQuizStatusEnum::NOT_STARTED;
        }
    }

    public function includeActions(GeneralQuiz $periodicTest)
    {
        $actions = [];

        if ($user = Auth::guard('api')->user()) {
            if (! GeneralQuizStudent::where([
                'student_id'    =>  $user->id,
                'general_quiz_id'    =>  $periodicTest->id,
            ])->exists() and $this->getStatus($periodicTest) != GeneralQuizStatusEnum::FINISHED) {
                //Start Periodic test
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.general-quizzes.periodic-test.students.post.start.periodic.test', ['periodicTest' => $periodicTest->id]),
                    'label' => trans('general_quizzes.Start Periodic Test'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::START_PERIODIC_TEST
                ];
            }
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }


    public function includeSubject($periodicTest)
    {
        $subject = $periodicTest->subject;

        return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
    }



    public function includeSections(GeneralQuiz $periodicTest)
    {
        $subjectSections = $periodicTest->sections;

        return $this->collection($subjectSections, new SubjectFormatSubjectTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }
}
