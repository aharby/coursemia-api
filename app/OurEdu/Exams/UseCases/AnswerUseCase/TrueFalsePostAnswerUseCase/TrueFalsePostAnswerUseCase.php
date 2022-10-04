<?php

namespace App\OurEdu\Exams\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class TrueFalsePostAnswerUseCase implements TrueFalsePostAnswerUseCaseInterface
{

    private $examRepository;
    private $examQuestionAnswerRepository;
    private $student;

    public function __construct(
        ExamRepositoryInterface $examRepository,
        ExamQuestionAnswerRepositoryInterface $examQuestionAnswerRepository
    ) {
        $this->examRepository = $examRepository;
        $this->examQuestionAnswerRepository = $examQuestionAnswerRepository;
    }


    /**
     * @param ExamRepository $examRepository
     * @param ExamQuestion $examQuestion
     * @param Collection $answers
     * @return mixed|void
     */
    public function postAnswer(ExamRepository $examRepository, ExamQuestion $examQuestion, Collection $answers)
    {
        $examType = $examRepository->getExamType();

        $this->student = Auth::guard('api')->user()->student;

        $mainQuestion = $examRepository->getQuestion($examQuestion);
        $isCorrectQuestionArray = [];
        $isCorrectQuestion = false;

        // delete previous answers for this student
        $this->examQuestionAnswerRepository->deleteQuestionAnswers($examQuestion, $this->student->id);

        foreach ($answers as $answer) {
            $answerText = !is_bool(
                $answer->answer_text
            ) ? ($answer->answer_text == 'true' ? true : false) : $answer->answer_text;
            $answerId = $answer->answer_id ?? null;

            $isCorrect = $this->checkCorrectAnswer($mainQuestion, $answerText, $answer->answer_id);
            $isCorrectQuestionArray[] = $isCorrect;

            $isCorrectBool = $isCorrect == true ? 1 : 0;

            $answerData = [
                'question_id' => $examQuestion->id,
                'is_correct_answer' => $isCorrectBool,
                'answer_text' => $answerText,

            ];
            if ($answerId) {
                $answerData['option_table_type'] = TrueFalseOption::class;
                $answerData['option_table_id'] = $answer->answer_id;
            }
            $examRepository->insertAnswers($examQuestion->id, $answerData);
        }
        if (in_array(false, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = true;
        }

        $examRepository->updateExamQuestion(
            $examQuestion->id,
            ['is_correct_answer' => (int)$isCorrectQuestion, 'is_answered' => 1]
        );

        switch ($examType) {
            case ExamTypes::PRACTICE:
                //attach prepare question to student if practicing
                $this->assignQuestionToStudentPractise($mainQuestion);
                break;
            case ExamTypes::COURSE_COMPETITION:
            case ExamTypes::COMPETITION:
                $examRepository->insertCompetitionQuestionResult($examQuestion->id, $isCorrectQuestion);
                break;
            case ExamTypes::INSTRUCTOR_COMPETITION:
                $examRepository->insertInstructorCompetitionQuestionResult($examQuestion->id, $isCorrectQuestion);
                break;
        }
    }

    public function checkCorrectAnswer($mainQuestion, $answerText, $answerId = null)
    {
        $this->student = Auth::guard('api')->user()->student;

        if ($answerId) {
            $trueFalseQuestion = $mainQuestion->options()->where('is_correct_answer', 1)->where('id', $answerId);
            if ($trueFalseQuestion->exists() && $mainQuestion->is_true == $answerText) {
                return true;
            }
        } elseif (!is_null($answerText) && (bool)$mainQuestion->is_true == (bool)$answerText) {
            return true;
        }
        return false;
    }

    private function assignQuestionToStudentPractise($mainQuestion)
    {
        $this->student->takenPracticeQuestions()
            ->attach($mainQuestion->prepareExamQuestion->id);
    }
}
