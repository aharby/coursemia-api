<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseNote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('course_notes')->delete();
        $courses = Course::get();
        foreach ($courses as $course){
            $index = 0;
            while ($index < 10 ){
                $country =[
                    'course_id' => $course->id,
                    'category_id'=>1,
                    'url'=> 'https://google.com',
                    'is_free_content'=> rand(0,1),
                    'title:en'=>"note ${index} en",
                    'title:ar'=>"note ${index} ar"
                ];
                CourseNote::create($country);
                $index++;
            }
        }
    }
}
