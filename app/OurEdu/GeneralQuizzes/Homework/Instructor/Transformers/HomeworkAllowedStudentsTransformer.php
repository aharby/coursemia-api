<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Classroom\Transformers\StudentTransformer;
use App\OurEdu\GeneralQuizzes\Lookup\Transformers\ClassroomLookUptransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizStatusEnum;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
use App\OurEdu\Quizzes\Student\Transformers\Homework\ClassroomHomeworkListTransformer;

class HomeworkAllowedStudentsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'classroom'
    ];
    protected array $availableIncludes = [
        'questions',
        'classroom'
    ];
    /**
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
        $this->generalQuizStudent = GeneralQuizStudent::where('student_id',$student->id)
                ->where('general_quiz_id',$this->generalQuiz->id)->first();
        return [
            'id' => (int)$student->id,
            'is_active' => (bool)$student->is_active,
            'is_attend' => isset($this->studentsAnswered[$student->id]),
            'score' => $this->generalQuizStudent ? $this->generalQuizStudent->score : 0,
            'score_percentage' => $this->generalQuizStudent ? $this->generalQuizStudent->score_percentage : 0,
            'name' => trim($student->name)
        ];
    }
    public function includeQuestions(User $student)
    {
        if(isset($this->studentsAnswered[$student->id])){

            if ($this->generalQuizStudent->generalQuiz->questions->count()) {
                $bankQuestions = $this->generalQuizStudent->generalQuiz->questions()->paginate(1);
                $questions = [];

                foreach ($bankQuestions as $question) {
                    if (isset($question->questions)) {
                        $questions[] = $question->questions;
                    }
                }
                return $this->collection($questions, new QuestionTransformer($this->generalQuiz,$this->student,["show_if_is_correct"=>true]), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);
            }
        }
    }

    public function includeClassroom(User $student){
        if ($student->student->classroom){
            return $this->item($student->student->classroom,new ClassroomLookUptransformer(),ResourceTypesEnums::CLASSROOM);
        }
    }
}
