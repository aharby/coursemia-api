<?php


namespace App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\UserEnums;

class GeneralUseCase implements GeneralUseCaseInterface
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

        if ($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER
            and $generalQuiz->school_account_id != $user->schoolAccount->id) {
            return [
                'message'=>trans("general_quizzes.you don't belong to this quiz", ['quiz_type' => trans($generalQuiz->quiz_type)]),
                'code' => '422'
            ];
        }

        if ($user->type == UserEnums::SCHOOL_LEADER and ($user->schoolLeader and $user->schoolLeader->id !== $generalQuiz->branch_id )) {
            return [
                'message'=>trans("general_quizzes.you don't belong to this quiz", ['quiz_type' => trans($generalQuiz->quiz_type)]),
                'code' => '422'
            ];
        }
        if ($user->type == UserEnums::SCHOOL_SUPERVISOR  and ($user->schoolSupervisor and $user->schoolSupervisor->id !== $generalQuiz->branch_id )) {
            return [
                'message'=>trans("general_quizzes.you don't belong to this quiz", ['quiz_type' => trans($generalQuiz->quiz_type)]),
                'code' => '422'
            ];
        }
        if ($user->type == UserEnums::ACADEMIC_COORDINATOR  and ($user->branch and $user->branch->id !== $generalQuiz->branch_id )) {
            return [
                'message'=>trans("general_quizzes.you don't belong to this quiz", ['quiz_type' => trans($generalQuiz->quiz_type)]),
                'code' => '422'
            ];
        }

        return [];
    }
}
