<?php

namespace App\Broadcasting;

use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Transformers\UserAuthTransformer;
use App\OurEdu\Users\User;

class CompetitionChannel
{
    use ApiResponser;

    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\OurEdu\Users\User  $user
     * @param  \App\OurEdu\Exams\Models\Exam $exam
     * @return array|bool
     */
    public function join(User $user , Exam $exam)
    {
//        $student = $user->student;
//        if ($exam->competitionStudents()->where('competition_student.student_id' , $student->id)->exists()) {
//
//            return $user->toArray();
//        }
//        return $this->transformDataModInclude($user, '', new UserAuthTransformer(), ResourceTypesEnums::USER, []);

        return  [
            'id' => (int) $user->id,
            'first_name' => (string) $user->first_name,
            'last_name' => (string) $user->last_name,
            'profile_picture' => (string) imageProfileApi($user->profile_picture),
        ];
    }
}
