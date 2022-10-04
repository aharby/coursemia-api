<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;

class RemoveParentsEmptyStudent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('type', UserEnums::PARENT_TYPE)->whereDoesntHave('students')->get();

        foreach ($users as $user) {
            $user->delete();
        }
    }
}
