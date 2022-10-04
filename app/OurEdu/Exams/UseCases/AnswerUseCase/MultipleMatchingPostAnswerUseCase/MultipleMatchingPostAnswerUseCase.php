<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\MultipleMatchingPostAnswerUseCase;


use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\ExamQuestionAnswer\ExamQuestionAnswerRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Swis\JsonApi\Client\Collection;

class MultipleMatchingPostAnswerUseCase implements MultipleMatchingPostAnswerUseCaseInterface
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
            $singleQuestionId = $answer->single_question_id;
            $isCorrect = $this->checkCorrectAnswer($singleQuestionId, $answer->answer_id);
            $isCorrectQuestionArray[] = $isCorrect;
            $answerData = [
                'question_id' => $examQuestion->id,
                'is_correct_answer' => $isCorrect,
                'option_table_type' => MultiMatchingOption::class,
                'option_table_id' => $answer->answer_id,
                'single_question_type' => MultiMatchingQuestion::class,
                'single_question_id' => $answer->single_question_id
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

    public function checkCorrectAnswer($singleQuestionId, $answerId)
    {
        return DB::table('res_multi_matching_question_option')
                ->where('res_multi_matching_question_id', $singleQuestionId)
                ->where('res_multi_matching_option_id', $answerId)
                ->count() > 0;
    }

    public function checkAllQuestionAnswerCount($mainQuestion, $examQuestion)
    {
        $questions = $mainQuestion->questions;
        foreach ($questions as $question) {
            $count = $question->options()->count();
            $answerCount = $examQuestion->answers()
                ->where('single_question_id', $question->id)
                ->pluck('option_table_id')->unique()->count();

            if ($count != $answerCount) {
                return false;
            }
        }
        return true;
    }

    private function assignQuestionToStudentPractise($mainQuestion)
    {
        $this->student->takenPracticeQuestions()
            ->attach($mainQuestion->prepareExamQuestion->id);
    }
}
