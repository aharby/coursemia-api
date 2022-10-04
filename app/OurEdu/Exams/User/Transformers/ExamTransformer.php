<?php


namespace App\OurEdu\Exams\User\Transformers;

use App\OurEdu\Exams\Models\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use League\Fractal\TransformerAbstract;

class ExamTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];
    private $params;
    private $timeToSolveFlag = false;

    public function __construct($params = [])
    {
        $this->params = $params;

    }

    public function transform(Exam $exam)
    {
        $timeToSolve = round($exam->time_to_solve);


        $transformerDatat = [
            'id' => (int)$exam->id,
            'title' => (string) trans('app.exam_on',['title'=>$exam->title]),
            'questions_numbers' => $exam->questions_number,
            'number_of_pages' => $exam->questions_number,
            'difficulty_level' => trans('difficulty_levels.'.$exam->difficulty_level),
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,

            'start_time' => $exam->start_time,
            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            'time_to_solve' => $timeToSolve,
            'student_time_to_solve' => round($exam->student_time_to_solve),
        ];

        return $transformerDatat;
    }
}
