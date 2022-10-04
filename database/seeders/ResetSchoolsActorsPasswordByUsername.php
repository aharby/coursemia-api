<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;

class ResetSchoolsActorsPasswordByUsername extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schoolActors = array_keys(UserEnums::schoolAccountUsers());
        $schoolActors[] = UserEnums::SCHOOL_LEADER;
        $schoolActors[] = UserEnums::SCHOOL_SUPERVISOR;
        $schoolActors[] = UserEnums::STUDENT_TYPE;
        $schoolActors[] = UserEnums::PARENT_TYPE;
        $schoolActors[] = UserEnums::SCHOOL_ACCOUNT_MANAGER;

        $users = User::query()->whereIn("type", $schoolActors)->get();

        foreach ($users as $user) {
            $user->password = $user->username;
            $user->save();
        }
    }
}
