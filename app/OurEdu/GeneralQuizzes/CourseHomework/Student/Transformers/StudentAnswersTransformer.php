<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Student\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
use App\OurEdu\Users\UserEnums;

class StudentAnswersTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'questions',
    ];

    public function __construct(GeneralQuiz $generalQuiz = null, User $student = null)
    {
        $this->generalQuiz = $generalQuiz;
        $this->student = $student;
    }

    public function transform(GeneralQuizStudent $student)
    {
        $data = [
            'id' => (int)$student->student_id,
            'score' => (float)$student->score,
            'score_percentage' => (float)$student->score_percentage,
        ];

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
