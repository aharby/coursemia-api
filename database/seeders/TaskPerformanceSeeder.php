<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Subjects\Models\SubModels\Task;

class TaskPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contentAuthors = ContentAuthor::factory()->count(20)->create();

        $subject = Subject::factory()->create();

        $contentAuthors->each(function ($author) use ($subject) {
            $author->tasks()->saveMany(Task::factory()->count(random_int(5, 20))->make(['subject_id' => $subject->id]));
        });

        dump($subject->id);
    }
}
