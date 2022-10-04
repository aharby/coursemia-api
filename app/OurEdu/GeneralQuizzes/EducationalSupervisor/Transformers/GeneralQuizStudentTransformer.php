<?php


namespace App\OurEdu\GeneralQuizzes\EducationalSupervisor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;

class GeneralQuizStudentTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
             'actions' , 'classroom'
    ];    /**
     * @var GeneralQuiz|null
     */
    private $generalQuiz;
    private $studentsAnswered;

    /**
     * StudentTransformer constructor.
     * @param GeneralQuiz|null $generalQuiz
     * @param User|null $student
     */
    public function __construct(GeneralQuiz $generalQuiz = null, $studentsAnswered = null)
    {

        $this->generalQuiz = $generalQuiz;
        $this->studentsAnswered = $studentsAnswered;
    }
    public function transform(User $student)
    {
        $this->generalQuizStudent = GeneralQuizStudent::query()
            ->where('student_id',$student->id)
            ->where('general_quiz_id',$this->generalQuiz->id)
            ->first();

        return [
            'id' => (int)$student->id,
            'is_active' => (bool)$student->is_active,
            'is_attend' => isset($this->studentsAnswered[$student->id]),
            'score' => $this->generalQuizStudent ? $this->generalQuizStudent->score : 0,
            'score_percentage' => $this->generalQuizStudent ? $this->generalQuizStudent->score_percentage : 0,
            'name' => trim($student->name)
        ];
    }

    public function includeActions(User $student)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.general-quizzes.educational-supervisor-reports.get.getStudentAnswers', ['generalQuiz' =>  $this->generalQuiz,'student' => $student]),
            'label' => trans('general_quizzes.view_student_answers'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_STUDENT_ANSWERS

        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
    public function includeClassroom(User $student)
    {
        if($student->student->classroom) {
            return $this->item($student->student->classroom, new ClassroomTransformer(), ResourceTypesEnums::CLASSROOM);
        }
    }
}
