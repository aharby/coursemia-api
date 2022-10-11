<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use Illuminate\Database\Seeder;
use App\Modules\Courses\Models\CourseImage;

class CourseImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courses = Course::get();
        foreach ($courses as $course){
            for ($i = 0; $i < 2; $i++){
                $course_image = new CourseImage;
                $course_image->course_id = $course->id;
                $course_image->image = "uploads/courses/course-1665005192.png";
                $course_image->save();
            }
        }
    }
}
