<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseReview;
use App\Modules\Users\User;
use Illuminate\Database\Seeder;

class CourseReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courses = Course::get();
        $users = User::get();
        foreach ($courses as $course){
            $course_rate = 0;
            foreach ($users as $user){
                $rate = rand(1,5);
                $course_review = new CourseReview;
                $course_review->user_id = $user->id;
                $course_review->course_id = $course->id;
                $course_review->rate = $rate;
                $course_review->save();
                $course_rate += $rate;
            }
            $course->rate = $course_rate / count($users);
            $course->save();
        }
    }
}
