<?php

namespace App\OurEdu\Exams\Instructor\Jobs;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetCompetitionAsStartedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Exam $exam)
    {
    }

    public function handle(ExamRepository $examRepository)
    {
        $data = [
            'is_started' => 1,
            'start_time' => now()
        ];

        $examRepository->update($this->exam, $data);
    }
}
