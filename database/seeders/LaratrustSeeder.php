<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
        $this->command->info('Truncating User, Role and Permission tables');

        $users = User::where('type', UserEnums::SCHOOL_INSTRUCTOR)->get();
        foreach ($users as $user) {
            if (!empty($user->username) && is_numeric($user->username)) {
                $user->password = $user->username;

                $user->save();
            }
        }
    }
}
