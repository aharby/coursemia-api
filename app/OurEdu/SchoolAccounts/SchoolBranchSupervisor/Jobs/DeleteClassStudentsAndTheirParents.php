<?php

namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Jobs;

use App\OurEdu\SchoolAccounts\Classroom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteClassStudentsAndTheirParents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Classroom
     */
    private $classroom;

    /**
     * Create a new job instance.
     *
     * @param Classroom $classroom
     */
    public function __construct(Classroom $classroom)
    {
        $this->classroom = $classroom;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // deleting the students users
        foreach ($this->classroom->students as $student) {

            if ($student->has('user') &&!empty($student->user)) {
                if (!empty($student->user)&&$student->user->has('parents')) {
                    foreach ($student->user->parents as $parent) {
                        if ($parent->students()->count() <= 1) {
                            $parent->delete();
                        } else {
                            $parent->students()->detach($student->user->id);
                        }
                    }
                }
                $student->user->delete();

            }

        }
        $this->classroom->students()->delete();
        $this->classroom->delete();
    }
}
