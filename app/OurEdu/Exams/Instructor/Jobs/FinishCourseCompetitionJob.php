<?php

namespace App\OurEdu\Exams\Instructor\Jobs;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Student\Jobs\StudentFinishedCompetitionJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinishCourseCompetitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Exam $exam)
    {
    }

    public function handle()
    {
        foreach ($this->exam->competitionStudents as $student) {
            StudentFinishedCompetitionJob::dispatch($this->exam, $student);
        }
    }

}
