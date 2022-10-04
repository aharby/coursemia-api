<?php

namespace App\Console\Commands;

use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Repository\TaskRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HandleQuestionTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'questions:time';

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
        $times = ExamQuestion::with('questionable')
            ->select(DB::raw('`slug`,`question_table_id`,`question_table_type`,avg(`student_time_to_solve`) as time_to_solve_avg'))
            ->groupBy('question_table_type', 'question_table_id', 'slug')
            ->get(['slug', 'question_table_id', 'question_table_type']);
        foreach ($times as $time) {
            $question = $time->questionable()->first();

            if ($question) {
                $question->update(['time_to_solve' => $time->time_to_solve_avg]);
                PrepareExamQuestion::where('question_table_id', $question->id)->where('question_table_type', get_class($question))
                    ->update(['time_to_solve' => $time->time_to_solve_avg]);


            }
        }

        return 0;

    }


}
