<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;

class FormativeTestQuestionsController extends BaseController
{
    public function __construct(private GeneralQuizRepositoryInterface $generalQuizRepository)
    {
        $this->middleware('type:school_admin');
    }

    public function index(GeneralQuiz $generalQuiz)
    {
        $generalQuiz->students_answered_count = $generalQuiz->studentsAnswered()->count();
        $data['page_title'] = trans('general_quizzes.questions');
        $data['breadcrumb'] = '';
        $data['generalQuiz'] = $generalQuiz;
        $data['questions'] = $this->generalQuizRepository->getGeneralQuizQuestionsPaginated($generalQuiz);

        return view("school_admin.formativeTest.questions.index", $data);
    }

    public function delete(GeneralQuiz $generalQuiz, GeneralQuizQuestionBank $generalQuizQuestionBank)
    {
        if (!is_null($generalQuiz->published_at)) {
            return redirect()->back()->with(['error' => trans("general_quizzes.cannot delete any questions quiz alredy published",
                ['quiz_type'=> trans('general_quizzes.'.$generalQuiz->quiz_type)])]);
        }

        $generalQuiz->questions()->detach($generalQuizQuestionBank->id);

        $this->generalQuizRepository->updateGeneralQuizMark($generalQuiz);

        return redirect()->back()->with(['success' => trans("app.Deleted Successfully")]);
    }
}
