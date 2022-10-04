<?php

namespace Database\Seeders;

use App\OurEdu\Exams\Models\PrepareExamQuestion;
use Illuminate\Database\Seeder;

class FixPrepareExamQuestion extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = PrepareExamQuestion::cursor();
        foreach ($questions as $question) {
            try {
                $task = $question->question->parentData->resourceSubjectFormatSubject->task;
                if ($task) {
                    $isDone = $task->is_done;
                    if ($isDone) {
                        $question->update(['is_done' => 1]);
                    } else {
                        if (in_array($task->subject_id, [49, 50, 51, 52, 132, 113, 15, 17, 88, 89, 91, 90])) {
                            $task->update(['is_done' => 1]);
                            $question->update(['is_done' => 1]);
                        }
                    }
                }
            } catch (Exception $exception) {
            }
        }
    }
}
