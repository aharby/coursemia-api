<?php


namespace App\OurEdu\GeneralQuizzes\Parent\Transformers;


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

class StudentGeneralQuizAnswersTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
        'questions',
    ];
    /**
     * @var GeneralQuiz|null
     */
    private $generalQuiz;
    private $student;

    /**
     * StudentGeneralQuizAnswersTransformer constructor.
     * @param GeneralQuiz|null $generalQuiz
     * @param User|null $student
     */
    public function __construct(GeneralQuiz $generalQuiz = null, User $student = null)
    {

        $this->generalQuiz = $generalQuiz;
        $this->student = $student;
    }

    public function transform(GeneralQuizStudent $student)
    {
        $data = [
            'id' => (int)$student->student_id,
            'score'=>(float)$student->score,
            'score_percentage'=>(float)$student->score_percentage,
            'is_reviewed'=>(bool)$student->is_reviewed,
        ];
        if ($student->user) {
            $data['name'] = (string)trim($student->user->name);
        }
        return $data;
    }
    public function includeQuestions(GeneralQuizStudent $generalQuizStudent)
    {
        if ($generalQuizStudent->generalQuiz->questions->count()) {
            $bankQuestions = $generalQuizStudent->generalQuiz->questions()->paginate(1);
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
