<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase;


use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Collection;

class MultiChoicePostAnswerUseCase implements MultiChoicePostAnswerUseCaseInterface
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
        $this->student = Auth::guard('api')->user()->student;
        $examType = $examRepository->getExamType();

        $mainQuestion = $examRepository->getQuestion($examQuestion);
        $isCorrectQuestionArray = [];
        $isCorrectQuestion = false;
        // delete previous answers for this student
        $this->examQuestionAnswerRepository->deleteQuestionAnswers($examQuestion, $this->student->id);
        foreach ($answers as $answer) {
            $isCorrect = $this->checkCorrectAnswer($mainQuestion, $answer->answer_id);
            $isCorrectQuestionArray[] = $isCorrect;
            $answerData = [
                'question_id' => $examQuestion->id,
                'is_correct_answer' => $isCorrect,
                'option_table_type' => MultipleChoiceOption::class,
                'option_table_id' => $answer->answer_id
            ];
            $examRepository->insertAnswers($examQuestion->id, $answerData);
        }

        $isCorrectQuestionArray[] = $this->checkAllQuestionAnswerCount($mainQuestion, $examQuestion);

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

    public function checkCorrectAnswer($mainQuestion, $answerId)
    {
        return $mainQuestion->options()->where('id', $answerId)->where('is_correct_answer', 1)->exists();
    }

    public function checkAllQuestionAnswerCount($mainQuestion, $examQuestion)
    {
        $this->student = Auth::guard('api')->user()->student;

        $count = $mainQuestion->options()->where('is_correct_answer', 1)->count();
        $answerCount = $examQuestion->answers()
            ->where('student_id', $this->student->id)
            ->pluck('option_table_id')
            ->unique()->count();
        return ($count == $answerCount) ? true : false;
    }

    private function assignQuestionToStudentPractise($mainQuestion)
    {
        $this->student->takenPracticeQuestions()
            ->attach($mainQuestion->prepareExamQuestion->id);
    }
}
