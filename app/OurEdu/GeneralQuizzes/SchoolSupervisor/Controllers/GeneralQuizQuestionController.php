<?php


namespace App\OurEdu\GeneralQuizzes\SchoolSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;

class GeneralQuizQuestionController extends BaseController
{
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;

    /**
     * GeneralQuizQuestionController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
    }

    public function list(GeneralQuiz $generalQuiz)
    {
        $data['questions'] = $this->generalQuizRepository->getGeneralQuizQuestionsPaginated($generalQuiz);
        $data['generalQuiz'] = $generalQuiz;

        return view("school_supervisor.generalQuizzes.questions.index", $data);
    }

    public function delete(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $question)
    {
        if (!is_null($generalQuiz->published_at)) {
            return redirect()->back()->with(['error' => trans("general_quizzes.cannot delete any questions quiz alredy published",
                ['quiz_type'=> trans('general_quizzes.'.$generalQuiz->quiz_type)])]);
        }

        $generalQuiz->questions()->detach($question->id);

        return redirect()->back()->with(['success' => trans("app.Deleted Successfully")]);
    }
}
