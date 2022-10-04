<?php


namespace App\OurEdu\Exams\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\ExamChallenge;
use App\OurEdu\Exams\Student\Transformers\QuestionTransformer;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRScheduleTransformer;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRSessionTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use League\Fractal\TransformerAbstract;

class ExamTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'questions',
        'recommendation',
        'actions',
        'instructors',
        'vcrSpot',
        'feedback',
        'challenges',
        'challenged',
    ];
    protected $useCaseData;
    private $params;
    private $timeToSolveFlag = false;

    public function __construct($params = [])
    {
        $this->params = $params;
        // if current route is start exam (student refresh exam), then calculate time to solve based on remaining time
        if(Route::currentRouteName() == 'api.student.exams.post.startExam'){
            $this->timeToSolveFlag = true;
        }
    }

    public function transform(Exam $exam)
    {
        $timeToSolve = round($exam->time_to_solve);
        // in case of refresh, time to solve updated to remaining time
        if($this->timeToSolveFlag){
            // get difference in seconds then subtract those seconds from time to solve
            $now = Carbon::now();
            $examStartTime = Carbon::parse($exam->start_time);
            $timeToSolve -= $now->diffInSeconds($examStartTime);
        }

        $transformerDatat = [
            'id' => (int)$exam->id,
            'title' => (string) trans('app.exam_on',['title'=>$exam->title]),
            'questions_numbers' => $exam->questions_number,
            'number_of_pages' => $exam->questions_number,
            'student' => $exam->student->user->first_name,
            'difficulty_level' => trans('difficulty_levels.'.$exam->difficulty_level),
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,

            'start_time' => $exam->start_time,
            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            'time_to_solve' => $timeToSolve,
            'student_time_to_solve' => round($exam->student_time_to_solve),
            'result' => (float) $exam->result .'%',
            'direction'=>$exam->subject->direction,
        ];

        if (isset($this->params['challenged']) and $exam->is_finished && !$exam->challenged()->count()) {

            $transformerDatat['challenge_link'] = getDynamicLink(DynamicLinksEnum::STUDENT_DYNAMIC_URL , [
                'firebase_url' => env('FIREBASE_URL_PREFIX'),
                'portal_url' => env('STUDENT_PORTAL_URL'),
                'android_apn' => env('ANDROID_APN','com.ouredu.students'),
                'link_name' => 'studentChallengeInExam',
                'query_param' =>'exam_id%3D'.$exam->id.'%26target_screen%3D'.DynamicLinkTypeEnum::CHALLENGE_STUDENT,
            ]);
        }

        return $transformerDatat;
    }

    public function includeVcrSpot($exam)
    {
        if ($exam->VCRRequest->count() == 0 && $exam->vcrSpot) {
            $exam->vcrSpot->exam_id = $exam->id;
            return $this->item($exam->vcrSpot, new VCRScheduleTransformer(), ResourceTypesEnums::VCR_SPOT);
        }
    }

    public function includeActions(Exam $exam)
    {
        $actions = [];

        if ($exam->is_started == 0) {
            $actions[] = [

                'endpoint_url' => buildScopeRoute('api.student.exams.post.startExam', ['examId' => $exam->id]),
                'label' => trans('exam.Start Exam'),
                'method' => 'POST',
                'key' => APIActionsEnums::START_EXAM
            ];
        }

        if (isset($this->params['challenged']) and $exam->is_finished && !$exam->challenged()->count()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.exams.post.challenge', ['examId' => $exam->id]),
                'label' => trans('exam.create challenge exam'),
                'method' => 'POST',
                'key' => APIActionsEnums::CREATE_CHALLENGE_EXAM
            ];
        }


        if (isset($this->params['view_exam'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.exams.get.viewExam', ['examId' => $exam->id]),
                'label' => trans('exam.view exam'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_EXAM
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.exams.get.retakeExam', ['examId' => $exam->id]),
                'label' => trans('exam.retake exam'),
                'method' => 'GET',
                'key' => APIActionsEnums::RETAKE_EXAM
            ];
        }

        if (isset($this->params['retake_exam'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.exams.get.retakeExam', ['examId' => $exam->id]),
                'label' => trans('exam.retake exam'),
                'method' => 'GET',
                'key' => APIActionsEnums::RETAKE_EXAM
            ];
        }

        if ($exam->inProgress()) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.exams.post.finishExam', ['examId' => $exam->id]),
                'label' => trans('exam.Finish exam'),
                'method' => 'POST',
                'key' => APIActionsEnums::FINISH_EXAM
            ];
        }
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeRecommendation(Exam $exam)
    {

     return $this->item($exam, new ExamReportRecommendationTransformer(), ResourceTypesEnums::EXAM_REPORT_RECOMMENDATION);

    }

    public function includeFeedback(Exam $exam)
    {
        return $this->item($exam, new ExamFeedBackTransformer(), ResourceTypesEnums::EXAM_FEEDBACK);
    }

    public function includeQuestions(Exam $exam)
    {
        $questions = $exam->questions;
        $params = [
            'is_answer' => true,
        ];
        if (isset($this->params['actions'])) {
            $params['actions'] = false;
        }
        return $this->collection($questions, new QuestionTransformer($params), ResourceTypesEnums::EXAM_QUESTION);
    }

    public function includeChallenges(Exam $exam) {
        $challenges = new Collection();
        $challengedExamsIds = $exam->challenges()
            ->pluck("exam_challenges.related_exam_id")
            ->toArray();
        $challengesExams = Exam::query()
            ->where("is_finished", "=", 1)
            ->whereIn("id", $challengedExamsIds)
            ->get();
        foreach ($challengesExams as $challengesExam) {
            $student = $challengesExam->student;
            $student->exam = $challengesExam;
            $challenges->push($student);
        }
        return $this->collection($challenges, new ChallengeTransformer(), ResourceTypesEnums::CHALLENGED_STUDENT);

    }
    public function includeChallenged(Exam $exam) {
        $challenges = new Collection();
        $challengedExamsIds = $exam->challenged()
            ->pluck("exam_challenges.exam_id")
            ->toArray();
        $challengedExams = Exam::query()
            ->where("is_finished", "=", 1)
            ->whereIn("id", $challengedExamsIds)
            ->get();
        foreach ($challengedExams as $challengedExam) {
            $student = $challengedExam->student;
            $student->exam = $challengedExam;
            $challenges->push($student);
        }
        return $this->collection($challenges, new ChallengeTransformer(), ResourceTypesEnums::CHALLENGED_STUDENT);
    }

    public function includeInstructors(Exam $exam)
    {
        if ($exam->instructorsVCR->count()) {
            return $this->collection(
                $exam->instructorsVCR,
                new InstructorsVCRTransformer($exam),
                ResourceTypesEnums::INSTRUCTOR_VCR
            );
        }
    }
}
