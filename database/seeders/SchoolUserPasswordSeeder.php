<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;

class SchoolUserPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return  void
     */

    public function run()
    {
        $this->command->info('update instractor password');

        $users = User::where('type', UserEnums::SCHOOL_INSTRUCTOR)->get();
        foreach ($users as $user) {
            if (!empty($user->username) && is_numeric($user->username)) {
                $user->password = $user->username;
                dump($user->username . ' ' . ':' . $user->password . '==>' . $user->id);
                $user->save();
            }
        }
    }
}
