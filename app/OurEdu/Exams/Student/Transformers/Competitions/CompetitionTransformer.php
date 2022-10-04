<?php


namespace App\OurEdu\Exams\Student\Transformers\Competitions;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Instructor\Transformers\CompetitionOrderdListTransformer;
class CompetitionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
        'competitionStudents',
        'competition_group_order',
        'competitionUser',
        'CompetitionOrderedStudents',
        'questions'

    ];
    protected $useCaseData;
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Exam $exam)
    {
        $student = auth()->user()->student;
        $pivot = $exam->competitionStudents()
            ->where('student_id',$student->id)->first();

        $totalCorrectAnswer = $pivot->pivot->result ?? 0;
        $examQuestionsCount =$exam->questions()->count();
        $result =  $totalCorrectAnswer/$examQuestionsCount ;

        $transformerData = [
            'id' => (int)$exam->id,
            'title' => (string)trans('app.competition_on:',[
                'title'=>$exam->title
            ]),
            'questions_number' => $exam->questions_number,
            'student' =>$student->user->name,
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,
            'difficulty_level' =>trans('difficulty_levels.'.$exam->difficulty_level),

            'start_time' => $exam->start_time,
//            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            "time_to_solve" =>  $exam->time_to_solve,
//            "student_time_to_solve" => $exam->student_time_to_solve,
            "avg_result" =>   $result ? (float)number_format($result * 100, 2, '.', '') .'%':'0%',
            "result" =>  (string) $totalCorrectAnswer ."/". $examQuestionsCount,
        ];
        $this->params['examQuestionsCount'] = $exam->questions()->count();
        if (isset($this->params['joinCompetition'])) {
            $transformerData['share_link'] = getDynamicLink(
                DynamicLinksEnum::STUDENT_DYNAMIC_URL,
                [
                    'link_name' => 'studentJoinCompetition',
                    'firebase_url' => env('FIREBASE_URL_PREFIX'),
                    'portal_url' => env('STUDENT_PORTAL_URL'),
                    'query_param' =>'competition_id%3D'.$exam->id.'%26target_screen%3D'.DynamicLinkTypeEnum::JOIN_COMPETITION,
                    'android_apn' => env('ANDROID_APN','com.ouredu.students')
                ]
            );
        }


        $transformerData['student_rank'] = ($pivot->pivot->is_finished) ? getOrdinal($pivot->pivot->rank):trans("exam.calculating rank in progress");

        return $transformerData;
    }

    public function includeActions(Exam $exam)
    {
        $studentId = auth()->user()->student->id;

        $actions = [];
        if (isset($this->params['actions'])) {
            if ($exam->is_started == 0 && ($exam->student_id == $studentId)) {
                $actions[] = [

                    'endpoint_url' => buildScopeRoute('api.student.competitions.post.startCompetition', ['competitionId' => $exam->id]),
                    'label' => trans('exam.Start competition'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::START_COMPETITION
                ];
            }

            if (isset($this->params['view_exam'])) {
                $actions[] = [

                    'endpoint_url' => buildScopeRoute('api.student.competitions.get.viewCompetition', ['competitionId' => $exam->id]),
                    'label' => trans('exam.View competition'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::VIEW_COMPETITION
                ];
            }
        }
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

//    public function includeCompetitionReport(Exam $exam)
//    {
//        return $this->item($exam, new CompetitionReportTransformer(), ResourceTypesEnums::COMPETITION_REPORT);
//    }

    public function includeCompetitionGroupOrder(Exam $exam){
        $students = $this->params['students'];
        return $this->collection($students, new CompetitionOrderdListTransformer($exam, $this->params), ResourceTypesEnums::COMPETITION_GROUP_ORDER);
    }


    public function includeCompetitionUser(Exam $exam){
        $student = $exam->competitionStudents()
            ->wherePivot('student_id' , auth()->user()->student->id )
            ->first();
        return $this->item($student, new CompetitionStudentTransformer($exam, $this->params), ResourceTypesEnums::COMPETITION_STUDENT);
    }

    public function includeCompetitionOrderedStudents(Exam $exam)
    {
        $students = $exam->competitionStudents()
            ->orderBy('result','desc')
            ->get();
        return $this->collection($students, new CompetitionStudentTransformer($exam, $this->params), ResourceTypesEnums::COMPETITION_STUDENT);
    }


    public function includeQuestions(Exam $exam)
    {
        $questions = $exam->questions;
        $params = [
            'is_answer' => true
        ];
        if (isset($this->params['actions'])) {
            $params['actions'] = false;
        }
        return $this->collection($questions, new CompetitionQuestionTransformer($params), ResourceTypesEnums::EXAM_QUESTION);
    }
    private function totalCorrectAnswer($exam, $student)
    {
        $totalCorrectAnswers = CompetitionQuestionStudent::where('student_id' , $student->id)
            ->where('exam_id' , $exam->id)
            ->sum('is_correct_answer');

        return $totalCorrectAnswers;
    }


    public function includeCompetitionStudents(Exam $exam){
        $students = $exam->competitionStudents()
            ->wherePivot('student_id' ,'!=', auth()->user()->student->id )
            ->get();
        return $this->collection($students, new CompetitionStudentTransformer($exam, $this->params), ResourceTypesEnums::COMPETITION_STUDENT);
    }

}
