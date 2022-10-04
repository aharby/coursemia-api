<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;

class UpdateEducationSupervisorSchoolBranchesToBeMany extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $educationalSupervisors = User::query()
            ->where("type", "=", UserEnums::EDUCATIONAL_SUPERVISOR)
            ->whereNotNull("branch_id")
            ->get();

        foreach ($educationalSupervisors as $user)
        {
            $user->branches()->syncWithoutDetaching([$user->branch_id]);
            $user->branch_id = null;
            $user->save();
        }
    }
}
