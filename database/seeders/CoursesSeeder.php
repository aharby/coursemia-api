<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($counter = 1; $counter <= 10; $counter++){
            $course = new Course;
            $course->title_en = "Course number $counter english title";
            $course->title_ar = "عنوان الكورس رقم $counter";
            $course->cover_image = "uploads/courses/course-1665005192.png";
            $course->description_en = "Course number $counter description";
            $course->description_ar = "وصف الكورس رقم $counter";
            $course->price = rand(250, 2500);
            $course->rate = rand(1,5);
            $course->expire_date = now()->addYear();
            $course->save();
        }
    }
}
