<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class PaginateStudentAnswer extends TransformerAbstract
{
    /**
     * @var GeneralQuiz
     */
    private $periodicTest;
    /**
     * @var User
     */
    private $student;

    /**
     * whichStudentAnsweredTransformer constructor.
     * @param GeneralQuiz $periodicTest
     * @param User $student
     */
    public function __construct(GeneralQuiz $periodicTest, User $student)
    {
        $this->periodicTest = $periodicTest;
        $this->student = $student;
    }

    public function transform()
    {
        $questions = $this->periodicTest->questions()->pluck("id")->toArray();


        $correctAnswers = GeneralQuizStudentAnswer::query()
            ->where("student_id", "=", $this->student->id)
            ->where("general_quiz_id", "=", $this->periodicTest->id)
            ->where("is_correct", true)
            ->whereIn("general_quiz_question_id", $questions)
            ->pluck("general_quiz_question_id")
            ->toArray();


        $list = [];
        $i = 0;
        foreach ($questions as $question) {
            $list[] = [
                "order" => (int)++$i,
                "is_correct" => (boolean)in_array($question, $correctAnswers),
                "question_id" => (int)$question,
            ];
        }

        return [
            'id' => Str::uuid(),
            "question" => $list
        ];
    }
}
