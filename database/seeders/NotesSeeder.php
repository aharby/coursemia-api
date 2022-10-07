<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseNote;
use Illuminate\Database\Seeder;

class NotesSeeder extends Seeder
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
            $note = new CourseNote;
            $note->category_id = 1;
            $note->course_id = $course->id;
            $note->url = 'https://google.com';
            $note->is_free_content = rand(0,1);
            $note->save();
        }
    }
}
