<?php


namespace App\OurEdu\SchoolAdmin\GeneralQuizzes\UseCases;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\UserEnums;

class GeneralQuizUseCase
{

    public function delete(GeneralQuiz $generalQuiz)
    {
        $error = $this->validateDeleteQuiz($generalQuiz);

        if(!empty($error)){
            return $error;
        }
        if($generalQuiz->delete()){
            return [
                'message'=>trans('general_quizzes.Deleted successfully'),
                'code' => '200'
            ];
        }
        return [
            'message'=>trans('app.Something went wrong'),
            'code' => '500'
        ];
    }

    private function validateDeleteQuiz(GeneralQuiz $generalQuiz)
    {
        $user = auth()->user();

        if ($user->type == UserEnums::SCHOOL_ADMIN
            and $generalQuiz->school_account_id != $user->schoolAdmin->currentSchool->id) {
            return [
                'message'=>trans("general_quizzes.you don't belong to this quiz", ['quiz_type' => trans($generalQuiz->quiz_type)]),
                'code' => '422'
            ];
        }

        return [];
    }
}
