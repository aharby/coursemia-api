<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Student\Transformers;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class whichStudentAnsweredTransformer extends TransformerAbstract
{
    /**
     * @var GeneralQuiz
     */
    private $homework;
    /**
     * @var User
     */
    private $student;

    /**
     * whichStudentAnsweredTransformer constructor.
     * @param GeneralQuiz $homework
     * @param User $student
     */
    public function __construct(GeneralQuiz $homework, User $student)
    {
        $this->homework = $homework;
        $this->student = $student;
    }

    public function transform()
    {
        $questions = $this->homework->questions()->pluck("id")->toArray();

        if ($this->homework->random_question == true) {
            $generalQuizStudent = GeneralQuizStudent::query()
                ->where("student_id", "=", $this->student->id)
                ->where('general_quiz_id', "=", $this->homework->id)
                ->first();

            $questions = $generalQuizStudent->questions_order ?? $questions;
        }


        $answeredQuestion = GeneralQuizStudentAnswer::query()
            ->where("student_id", "=", $this->student->id)
            ->where("general_quiz_id", "=", $this->homework->id)
            ->whereIn("general_quiz_question_id", $questions)
            ->pluck("general_quiz_question_id")
            ->toArray();


        $list = [];
        $i = 0;
        foreach ($questions as $question) {
            $list[] = [
                "order" => (int)++$i,
                "is_solved" => (boolean)in_array($question, $answeredQuestion),
                "question_id" => (int)$question,
            ];
        }

        return [
            'id' => Str::uuid(),
            "question" => $list
        ];
    }
}
