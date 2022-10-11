<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseFlashcard;
use Illuminate\Database\Seeder;

class FlashcardsSeeder extends Seeder
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
            $category = Category::inRandomOrder()->first();
            $flash = new CourseFlashcard();
            $flash->course_id = $course->id;
            $flash->category_id = $category->id;
            $flash->front_en = "Front english";
            $flash->front_ar = "الوجه الأمامي";
            $flash->back_en = "Back english";
            $flash->back_ar = "الوجه الخلفي";
            $flash->answer = rand(0,1);
            $flash->save();
        }
    }
}
