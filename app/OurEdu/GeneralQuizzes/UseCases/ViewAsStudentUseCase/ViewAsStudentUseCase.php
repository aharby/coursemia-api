<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase;

use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class ViewAsStudentUseCase implements ViewAsStudentUseCaseInterface
{
    protected ?Authenticatable $user;
    protected GeneralQuizRepositoryInterface $generalQuizRepo;
    protected GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository;

    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepo, GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository)
    {
        $this->user = Auth::guard('api')->user();
        $this->generalQuizRepo = $generalQuizRepo;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
    }

    public function nextOrBackQuestion(int $generalQuizId, int $page):array
    {
        $generalQuiz = $this->generalQuizRepo->findOrFail($generalQuizId);

        $generalQuizRepo = new GeneralQuizRepository($generalQuiz);

        $bankQuestions = $generalQuizRepo->returnQuestionsViewAs($page);

        $validationError = $this->validateNextOrBackQuestion($bankQuestions);

        if (count($validationError) > 0) {
            return $validationError;
        }

        if ($bankQuestions->currentPage() == $bankQuestions->lastPage()) {
            $return['last_question'] = true;
        }
        $questions = [];
        foreach ($bankQuestions as $question) {
            if (isset($question->question)) {
                $questions[] = $question->question;
            }
        }
        $return['status'] = 200;
        $return['questions'] = $questions;
        $return['generalQuiz'] = $generalQuiz;
        $return['bankQuestions'] = $bankQuestions;
        return $return;
    }


    private function validateNextOrBackQuestion($bankQuestions) :array
    {
        $return = [];
        if ($bankQuestions->currentPage() > $bankQuestions->lastPage()) {
            $return['status'] = 422;
            $return['detail'] = trans('exam.This question not found');
            $return['title'] = 'This question not found';
            return $return;
        }
        return $return;
    }
}
