<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\DragDropPostAnswerUseCase;


use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepository;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class DragDropPostAnswerUseCase implements DragDropPostAnswerUseCaseInterface
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
            $singleQuestionId = $answer->single_question_id;
            $isCorrect = $this->checkCorrectAnswer($mainQuestion, $singleQuestionId, $answer->answer_id);
            $isCorrectQuestionArray[] = $isCorrect;
            $answerData = [
                'question_id' => $examQuestion->id,
                'is_correct_answer' => $isCorrect,
                'option_table_type' => DragDropOption::class,
                'option_table_id' => $answer->answer_id,
                'single_question_type' => DragDropQuestion::class,
                'single_question_id' => $answer->single_question_id
            ];
            $examRepository->insertAnswers($examQuestion->id, $answerData);
        }

        if (in_array(false, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = false;
        } elseif (in_array(true, $isCorrectQuestionArray, true)) {
            $isCorrectQuestion = true;
        }
        $examRepository->updateExamQuestion(
            $examQuestion->id,
            ['is_correct_answer' => $isCorrectQuestion, 'is_answered' => 1]
        );

        switch ($examType) {
            case ExamTypes::PRACTICE:
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

    public function checkCorrectAnswer($mainQuestion, $singleQuestionId, $answerId)
    {
        return $mainQuestion->questions()->where('id', $singleQuestionId)->where(
            'correct_option_id',
            $answerId
        )->exists();
    }

    private function assignQuestionToStudentPractise($mainQuestion)
    {
        $this->student->takenPracticeQuestions()
            ->attach($mainQuestion->prepareExamQuestion->id);
    }
}
