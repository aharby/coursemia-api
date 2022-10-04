<?php
namespace App\OurEdu\GeneralQuizzes\Jobs;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ValidateGeneralQuizMarkJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $generalQuiz;

    public function __construct(GeneralQuiz $generalQuiz)
    {
        $this->generalQuiz = $generalQuiz;
    }

    public function handle()
    {
        $quizMark = $this->generalQuiz->questions()->pluck('grade')->sum();
        if($quizMark != $this->generalQuiz->mark){
            $this->generalQuiz->update(['mark' => $quizMark]);
        }
    }
}
