<?php


namespace App\OurEdu\BaseNotification\Jobs;


use App\OurEdu\GeneralQuizzes\Jobs\HandelRepeatedQuestionsAnswers;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FinishGeneralQuizStudentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;

    /**
     * Create a new job instance
     * @param GeneralQuiz $generalQuiz
     */
    public function __construct(GeneralQuiz $generalQuiz)
    {
        $this->generalQuiz = $generalQuiz;
    }


    public function handle()
    {
        $endTime = new Carbon($this->generalQuiz->end_at);
        if ($endTime->isFuture()) {
            $this->release($endTime->addMinute()->diffInSeconds(Carbon::now()));

            return;
        }

        $studentsAnswers=$this->generalQuiz->studentsAnswered()
            ->where('is_finished', "!=", 1)
            ->get();

        foreach ($studentsAnswers as $studentsAnswer) {
            $studentsAnswer->is_finished = 1;
            $studentsAnswer->finish_at = Carbon::now();
            $studentsAnswer->save();
            HandelRepeatedQuestionsAnswers::dispatch($this->generalQuiz,$studentsAnswer);
        }

    }
}
