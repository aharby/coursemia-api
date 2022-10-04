<?php

namespace App\Console\Commands;

use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Users\User;
use Illuminate\Console\Command;

class TaskExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'task:expire';

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
        $taskRepo = new TaskRepository(new Task());

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
