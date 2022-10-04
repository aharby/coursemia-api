<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\CompletePostAnswerUseCase;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class CompletePostAnswerUseCase implements CompletePostAnswerUseCaseInterface
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

        // delete previous answers for this student
        $this->examQuestionAnswerRepository->deleteQuestionAnswers($examQuestion, $this->student->id);

        foreach ($answers as $answer) {
            $answerText = $answer->answer_text;

            $foundedAnswer = $this->checkCorrectAnswer($mainQuestion, $answerText);

            $answerData = [
                'question_id' => $examQuestion->id,
                'answer_text' => $answerText,
                'is_correct_answer' => (bool)$foundedAnswer,
                'option_table_type' => $foundedAnswer ? get_class($foundedAnswer) : null,
                'option_table_id' => $foundedAnswer->id ?? null,
            ];

            $examRepository->insertAnswers($examQuestion->id, $answerData);

            $examRepository->updateExamQuestion(
                $examQuestion->id,
                ['is_correct_answer' => (bool)$foundedAnswer, 'is_answered' => 1]
            );

            switch ($examType) {
                case ExamTypes::PRACTICE:
                    $this->assignQuestionToStudentPractise($mainQuestion);
                    break;
                case ExamTypes::COURSE_COMPETITION:
                case ExamTypes::COMPETITION:
                    $examRepository->insertCompetitionQuestionResult($examQuestion->id, (bool)$foundedAnswer);
                    break;
                case ExamTypes::INSTRUCTOR_COMPETITION:
                    $examRepository->insertInstructorCompetitionQuestionResult($examQuestion->id, (bool)$foundedAnswer);
                    break;
            }
        }
    }

    public function checkCorrectAnswer($mainQuestion, $answerText, $answerId = null)
    {
        $answer = $mainQuestion->answer;

        $mainQuestionAnswer = trim(strip_tags($answer->answer));
        $acceptedAnswers = $mainQuestion->acceptedAnswers->map(function ($data) {
            $data['answer'] = trim(strip_tags($data['answer']));
            return $data;
        })->pluck('answer')->toArray();

        return ($mainQuestionAnswer === $answerText) ? $answer
            : (in_array($answerText, $acceptedAnswers) ?
                $mainQuestion->acceptedAnswers()->where('answer', $answerText)->first()
                : false);
    }

    private function assignQuestionToStudentPractise($mainQuestion)
    {
        $this->student->takenPracticeQuestions()
            ->attach($mainQuestion->prepareExamQuestion->id);
    }
}
