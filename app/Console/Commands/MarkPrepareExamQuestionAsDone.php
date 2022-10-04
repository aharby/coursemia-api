<?php

namespace App\Console\Commands;

use App\OurEdu\Exams\Models\PrepareExamQuestion;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkPrepareExamQuestionAsDone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare-exam-question-done';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MarkPrepareExamQuestionAsDone';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $questions = PrepareExamQuestion::where('is_done', 0)->cursor();
        foreach ($questions as $question) {
            try {
                $task = $question->question->parentData->resourceSubjectFormatSubject->task;
                if ($task) {
                    $isDone = $task->is_done;
                    if ($isDone) {
                        $question->update(['is_done' => 1]);
                    }
                }
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }
}
