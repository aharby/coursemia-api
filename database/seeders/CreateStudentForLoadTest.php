<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CreateStudentForLoadTest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->with('student')->find(39321);
        for ($i = 1; $i <= 5000; $i++) {
            $username = 'loadTest'. $i;
            $newUser = $user->replicate(['username','password']);
            $newUser->username = $username;
            $newUser->password = $username;
            $newUser->save();
            $newUser->student()->create(Arr::except($user->student->toArray(),'id'));
        }
    }
}
