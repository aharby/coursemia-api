<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Users\Models\User;
use Illuminate\Database\Seeder;

class CourseUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::get();
        $courses = Course::get();
        foreach ($users as $user){
            foreach ($courses as $course){
                $course_user = new CourseUser;
                $course_user->course_id = $course->id;
                $course_user->user_id = $user->id;
                $course_user->save();
            }
        }
    }
}
