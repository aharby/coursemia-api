<?php


namespace App\OurEdu\Exams\UseCases\AnswerUseCase\PostAnswerUseCase;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Events\CompetitionEvents\CompetitionQuestionAnswered;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\UseCases\AnswerUseCase\HotSpotPostAnswerUseCase\HotSpotPostAnswerUseCaseInterface;
use Swis\JsonApi\Client\Collection;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepositoryInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\CompletePostAnswerUseCase\CompletePostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\DragDropPostAnswerUseCase\DragDropPostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MatchingPostAnswerUseCase\MatchingPostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\TrueFalsePostAnswerUseCase\TrueFalsePostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MultiChoicePostAnswerUseCase\MultiChoicePostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\AnswerUseCase\MultipleMatchingPostAnswerUseCase\MultipleMatchingPostAnswerUseCaseInterface;

class PostAnswerUseCase implements PostAnswerUseCaseInterface
{
    private $examRepository;
    private $examQuestionRepository;
    private $trueFalsePostAnswerUseCase;
    private $multiChoicePostAnswerUseCase;
    private $dragDropPostAnswerUseCase;
    private $matchingPostAnswerUseCase;
    private $multipleMatchingPostAnswerUseCase;
    private $completePostAnswerUseCase;
    private $hotSpotPostAnswerUseCase;

    public function __construct(
        ExamRepository $examRepository,
        ExamQuestionRepositoryInterface $examQuestionRepository,
        TrueFalsePostAnswerUseCaseInterface $trueFalsePostAnswerUseCase,
        MultiChoicePostAnswerUseCaseInterface $multiChoicePostAnswerUseCase,
        DragDropPostAnswerUseCaseInterface $dragDropPostAnswerUseCase,
        MatchingPostAnswerUseCaseInterface $matchingPostAnswerUseCase,
        MultipleMatchingPostAnswerUseCaseInterface $multipleMatchingPostAnswerUseCase,
        CompletePostAnswerUseCaseInterface $completePostAnswerUseCase,
        HotSpotPostAnswerUseCaseInterface $hotSpotPostAnswerUseCase
    ) {
        $this->examRepository = $examRepository;
        $this->examQuestionRepository = $examQuestionRepository;
        $this->trueFalsePostAnswerUseCase = $trueFalsePostAnswerUseCase;
        $this->multiChoicePostAnswerUseCase = $multiChoicePostAnswerUseCase;
        $this->dragDropPostAnswerUseCase = $dragDropPostAnswerUseCase;
        $this->matchingPostAnswerUseCase = $matchingPostAnswerUseCase;
        $this->multipleMatchingPostAnswerUseCase = $multipleMatchingPostAnswerUseCase;
        $this->completePostAnswerUseCase = $completePostAnswerUseCase;
        $this->hotSpotPostAnswerUseCase = $hotSpotPostAnswerUseCase;
    }

    /**
     * @param int $examId
     * @param Collection $data
     * @return Exam|void|null
     */
    public function postAnswer(int $examId, Collection $data)
    {
        if ($data && $data->isNotEmpty()) {
            $exam = $this->examRepository->findOrFail($examId);
            $examRepository = new ExamRepository($exam);

            $data->each(function ($question) use ($examRepository , $exam) {
                $questionId = $question->getId() ?? null;
                $answers = $question->answers ?? new Collection();
                if ($questionId) {
                    $questionObj = $examRepository->findOrFailExamQuestion($questionId);

                    switch ($questionObj->slug) {

                        case (LearningResourcesEnums::TRUE_FALSE):
                            $this->trueFalsePostAnswerUseCase->postAnswer($examRepository, $questionObj, $answers);
                            break;

                        case (LearningResourcesEnums::MULTI_CHOICE):
                            $this->multiChoicePostAnswerUseCase->postAnswer($examRepository, $questionObj, $answers);
                            break;

                        case (LearningResourcesEnums::DRAG_DROP):
                            $this->dragDropPostAnswerUseCase->postAnswer($examRepository, $questionObj, $answers);
                            break;

                        case (LearningResourcesEnums::MATCHING):
                            $this->matchingPostAnswerUseCase->postAnswer($examRepository, $questionObj, $answers);
                            break;

                        case (LearningResourcesEnums::MULTIPLE_MATCHING):
                            $this->multipleMatchingPostAnswerUseCase->postAnswer($examRepository, $questionObj, $answers);
                            break;


                        case (LearningResourcesEnums::COMPLETE):
                            $this->completePostAnswerUseCase->postAnswer($examRepository, $questionObj, $answers);
                            break;

                        case (LearningResourcesEnums::HOTSPOT):
                            $this->hotSpotPostAnswerUseCase->postAnswer($examRepository, $questionObj, $answers);
                            break;

                    }

                    $this->checkIfAllAnsweredCompetitionQuestion($exam , $questionId);
                }
            });

            return $exam;
        }
    }

    //TODO:: to be refactored (use repository and separate the case in other usecase)
    public function checkIfAllAnsweredCompetitionQuestion($exam , $questionID) {
        if ($exam->type == ExamTypes::COMPETITION || $exam->type == ExamTypes::COURSE_COMPETITION) {
            $competitorsCount = $exam->competitionStudents()->count();
            $competitionQuestionAnswers = CompetitionQuestionStudent::where(
                [
                    'exam_id' => $exam->id,
                    'exam_question_id' => $questionID
                ]
            )->get();

            if ($competitorsCount == count($competitionQuestionAnswers)) { //All Answered
                $correctRatio = ceil(($competitionQuestionAnswers->where('is_correct_answer' , 1)->count()/count($competitionQuestionAnswers)) * 100);
                $notCorrectRatio = 100 - $correctRatio; //To Avoid Fractions (ex correct ration 33.3333)
                event(new CompetitionQuestionAnswered($exam->id , $questionID , $correctRatio , $notCorrectRatio));
            }
        }
    }
}
