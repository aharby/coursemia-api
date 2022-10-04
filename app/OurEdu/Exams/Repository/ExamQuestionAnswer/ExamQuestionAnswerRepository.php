<?php


namespace App\OurEdu\Exams\Repository\ExamQuestionAnswer;


use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;

class ExamQuestionAnswerRepository implements ExamQuestionAnswerRepositoryInterface
{

    /**
     * @param array $data
     * @return ExamQuestionAnswer|null
     */
    public function create(array $data): ?ExamQuestionAnswer
    {
        return ExamQuestionAnswer::create($data);
    }

    public function deleteQuestionAnswers(ExamQuestion $examQuestion, int $studentId)
    {
         $examQuestion->answers()
                        ->where('student_id',$studentId)
                        ->delete();
    }
}
