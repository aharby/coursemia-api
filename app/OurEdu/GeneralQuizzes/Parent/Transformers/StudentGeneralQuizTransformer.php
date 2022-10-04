<?php

namespace App\OurEdu\GeneralQuizzes\Parent\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\Quizzes\Parent\QuizzesPerformance;
use App\OurEdu\Quizzes\Quiz;
use Carbon\Carbon;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentGeneralQuizTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralQuiz $generalQuiz)
    {
        $generalQuizStudent = $generalQuiz->studentsAnswered->first();

        $transformedData = [
            'id' =>  $generalQuiz->id,
            'title'=>(string)$generalQuiz->title,
            'start_date' => Carbon::parse($generalQuiz->start_at)->format("Y/m/d") ,
            'start_time' => Carbon::parse($generalQuiz->start_at)->format("H:i") ,
            'general_quiz_id' => $generalQuiz->id ,
            'show_result'=>(bool) $generalQuizStudent ? ($generalQuizStudent->show_result == 1 ? true:false ) :false,
            'quiz_type' => (string)$generalQuiz->quiz_type,
            'subject' => (string)$generalQuiz->subject->name ?? "",
            'is_attend' => (bool)!is_null($generalQuizStudent),
            'instructor'=>(string)$generalQuiz->creator->name,
            'time' => Carbon::parse($generalQuiz->start_at)->diffInMinutes(Carbon::parse($generalQuiz->end_at)),
        ];
        if(!is_null($generalQuizStudent)){
            $transformedData['started_at'] = (string)$generalQuizStudent->start_at;
            $transformedData['finished_at'] = (string)$generalQuizStudent->finish_at;
            $transformedData['is_finished'] = (bool) $generalQuizStudent->is_finished ==1?true:false;
            $transformedData["student_time"] = (string)$this->calculateStudentTime($generalQuizStudent);
        }

        if($generalQuizStudent && $generalQuizStudent->show_result){
            $transformedData['quiz_result_percentage'] = (float)$generalQuizStudent->score_percentage;
            $transformedData["order"] = (string)$generalQuizStudent->student_order;
        }

        return $transformedData;
    }

    private function calculateStudentTime(GeneralQuizStudent $generalQuizStudent)
    {
        $startedAt = $generalQuizStudent->start_at ?? null ;
        $finishedAt = $generalQuizStudent->finish_at ?? null ;

        if ($startedAt and $finishedAt) {
            return Carbon::parse($startedAt)->diffInMinutes(Carbon::parse($finishedAt));
        }

        return 0;
    }

    public function includeActions(GeneralQuiz $generalQuiz)
    {
        $actions = [];
        $generalQuizStudent = $generalQuiz->studentsAnswered->first();
        if($generalQuizStudent && $generalQuizStudent->show_result) {

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.general-quizzes.parent.listStudentGeneralQuizAnswers',
                    ['general_quiz' => $generalQuiz,
                        'student' => $this->params['student']]),
                'label' => trans('general_quizzes.view_student_answers'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_STUDENT_ANSWERS
            ];
        }

    if (count($actions)) {
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
    }
}
