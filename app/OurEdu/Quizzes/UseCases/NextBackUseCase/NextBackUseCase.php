<?php


namespace App\OurEdu\Quizzes\UseCases\NextBackUseCase;

use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class NextBackUseCase implements NextBackUseCaseInterface
{
    private $quizRepository;
    private $user;

    public function __construct(
        QuizRepositoryInterface $quizRepository
    ) {
        $this->quizRepository = $quizRepository;
        $this->user = Auth::guard('api')->user();
    }

    public function nextOrBackQuestion(int $quizId, int $page)
    {
        $quiz = $this->quizRepository->findOrFail($quizId);
        $quizRepo = $this->quizRepository->setQuiz($quiz);
        $quiz = $quizRepo->getQuiz();
        $studentQuiz = $this->user->student->quizzes()->where('quiz_id', $quiz->id)->first();

        if ($studentQuiz->status == QuizStatusEnum::STARTED) {
            $nextOrBackQuestion = $quizRepo->returnQuestion($page,  $studentQuiz->questions_ids);
            if ($nextOrBackQuestion->currentPage() > $nextOrBackQuestion->lastPage()) {
                $returnArr['status'] = 404;
                $returnArr['detail'] = trans('exam.This question not found');
                $returnArr['title'] = 'This question not found';
                return $returnArr;
            } else {
                if ($nextOrBackQuestion->currentPage() == $nextOrBackQuestion->lastPage()) {
                    $returnArr['last_question'] = true;
                }
                $returnArr['status'] = 200;
                $returnArr['questions'] = $nextOrBackQuestion;
                return $returnArr;
            }
        } else {
            $returnArr['status'] = 422;
            $returnArr['detail'] = trans('quiz.This quiz didnt start yet');
            $returnArr['title'] = 'This quiz didnt start yet';
            return $returnArr;
        }
    }
}
