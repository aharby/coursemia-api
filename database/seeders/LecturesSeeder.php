<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseLecture;
use Illuminate\Database\Seeder;

class LecturesSeeder extends Seeder
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
            $lecture = new CourseLecture();
            $lecture->course_id = $course->id;
            $lecture->category_id = 1;
            $lecture->url = 'https://google.com';
            $lecture->title_en = "English title";
            $lecture->title_ar = "عنوان المحاضره";
            $lecture->description_en = "description english";
            $lecture->description_ar = "وصف المحاضره";
            $lecture->is_free_content = rand(0,1);
            $lecture->save();
        }
    }
}
