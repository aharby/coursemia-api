<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Users\Models\User;
use App\Modules\WantToLearn\Models\WantToLearn;
use Illuminate\Database\Seeder;

class WantToLearnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lectures = CourseLecture::inRandomOrder()->take(10)->get()->pluck('id');
        $users = User::get()->pluck('id');
        foreach ($lectures as $lecture)
        {
            foreach ($users as $user)
            {
                $want = new WantToLearn;
                $want->lecture_id = $lecture;
                $want->user_id = $user;
                $want->save();
            }
        }
    }
}
