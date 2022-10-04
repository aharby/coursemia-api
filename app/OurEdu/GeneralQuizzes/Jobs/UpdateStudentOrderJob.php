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

class UpdateStudentOrderJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $generalQuiz;

    public function __construct(GeneralQuiz $generalQuiz)
    {
        $this->generalQuiz = $generalQuiz;
    }

    public function handle()
    {
        DB::statement("update general_quiz_students join (SELECT id, score, general_quiz_id,FIND_IN_SET( score, (
                SELECT GROUP_CONCAT(DISTINCT score ORDER BY score DESC ) FROM general_quiz_students WHERE general_quiz_id = ".$this->generalQuiz->id.")
                ) AS R FROM general_quiz_students WHERE general_quiz_id = ".$this->generalQuiz->id." ) AS K USING(id) SET general_quiz_students.order = K.R");

    }


}
