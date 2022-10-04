<?php

namespace App\Console\Commands;

use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepository;
use Illuminate\Console\Command;

class QuestionReportTaskExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'QRTask:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'QRTask:expire';

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
     * @return mixed
     */
    public function handle()
    {
        $taskRepo = new QuestionReportTaskRepository(new QuestionReportTask());
        $tasks = $taskRepo->getAssignedAndNotExpiredTasks();
        $tasksIds = [];

        foreach ($tasks as $task) {
            if ($task->pulled_at && $task->pulled_at->addDays($task->due_date) > now()) {
                $tasksIds[] = $task->id;
            }
        }

        $taskRepo->makeTasksExpired($tasksIds);

        return 0;

    }
}
