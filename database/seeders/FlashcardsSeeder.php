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
        $data = [];
        $courses = Course::all();
        $categories = Category::pluck('id');
        foreach ($courses as $course) {
            $flash =
                [
                    'course_id' => $course->id,
                    'category_id' => $categories->random(),
                    'front:en' => "Front english",
                    'front:ar' => "الوجه الأمامي",
                    'back:en' => "Back english",
                    'back:ar' => "الوجه الخلفي",
                ];
            array_push($data, $flash);
        }
        CourseFlashcard::insert($data);
    }
}
