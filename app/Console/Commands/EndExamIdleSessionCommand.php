<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Enums\ExamEnums;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;

class EndExamIdleSessionCommand extends Command
{
    protected $idleExamsCount = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:idle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End exams idle sessions';

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
        $exams = Exam::where([
            'is_finished' => false,
            'is_started'    =>  true
        ])
        ->has('examQuestions')
        ->with(['examQuestions' => function ($q) {
            $q->orderBy('updated_at', 'DESC');
        }])
        ->get();

        $exams->each(function ($exam) {
            // check last activity
            if ($exam->examQuestions->first()->updated_at < now()->subMinutes(ExamEnums::IDLE_TIME_TO_END_SESSION)) {
                // calculate result and end exam

                $total = $exam->examQuestions->count();
                $correctAnswers = $exam->examQuestions->where('is_correct_answer', 1)->count();

                $percentage = $total ? ($correctAnswers / $total) * 100 : 0;

                $exam->update([
                    'is_finished' => 1,
                    'finished_time' => now(),
                    'result'    =>  number_format($percentage, 2, '.', '')
                ]);

                $this->idleExamsCount++;
            }
        });


        $this->info("{$this->idleExamsCount} idle exams reseted.");
        return 0;

    }
}
