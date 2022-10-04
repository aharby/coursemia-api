<?php


namespace App\OurEdu\BaseNotification\Jobs;


use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateGeneralQuizScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;
    /**
     * @var GeneralQuizStudent
     */
    private $generalQuizStudent;
    /**
     * Create a new job instance
     * @param GeneralQuiz $generalQuiz
     * @param GeneralQuizStudent $generalQuizStudent
     */
    public function __construct(GeneralQuiz $generalQuiz,GeneralQuizStudent $generalQuizStudent)
    {

        $this->generalQuiz = $generalQuiz;
        $this->generalQuizStudent = $generalQuizStudent;
    }


    public function handle()
    {
        $generalQuizStudent = $this->generalQuizStudent;
        $mark = $this->generalQuiz->mark;
        $score= GeneralQuizStudentAnswer::query()
            ->where('student_id' , $generalQuizStudent->student_id)
            ->where('general_quiz_id', $this->generalQuiz->id)
            ->where('is_correct' , 1)
            ->sum('score');

        $score_percentage = $mark ? ($score / $mark) * 100 : 0;
        $data = [
            'score_percentage'    =>  number_format($score_percentage, 2, '.', ''),
            'score' => $score
        ];
        GeneralQuizStudent::query()->where("id", "=", $generalQuizStudent->id)->update($data);
    }
}
