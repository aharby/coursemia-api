<?php


namespace App\OurEdu\Exams\Student\Transformers\Practices;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Student\Transformers\ExamFeedBackTransformer;
use App\OurEdu\Exams\Student\Transformers\LiveSessionTransformer;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use League\Fractal\TransformerAbstract;

class PracticeTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];

    protected array $availableIncludes = [
        'questions',
        'actions',
//        'vcrSpot',
        'feedback',
    ];

    protected $useCaseData;
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Exam $exam)
    {
        $transformerDatat = [
            'id' => (int)$exam->id,
            'title' => (string) $exam->title,
            'questions_number' => $exam->questions_number,
            'student' => $exam->student->user->first_name,
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,
            'direction'=>$exam->subject ? $exam->subject->direction : '',
            'start_time' => $exam->start_time,
            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            'result' => (string)round($exam->result * (100 / 100),2)
        ];


        return $transformerDatat;
    }

    public function includeVcrSpot($exam)
    {
        if ($exam->vcrSpot) {
            return $this->item($exam->vcrSpot, new LiveSessionTransformer(), ResourceTypesEnums::LIVE_SESSION);
        }
    }

    public function includeActions(Exam $exam)
    {
        $actions = [];

        if (! $exam->is_started) {
            $actions[] = [

                    'endpoint_url' => buildScopeRoute('api.student.practices.post.startPractice', ['practiceId' => $exam->id]),
                    'label' => trans('exam.Start Practice'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::START_PRACTICE
                ];
        }

        if ($exam->inProgress()) {
            $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.practices.post.finishPractice', ['practiceId' => $exam->id]),
                    'label' => trans('exam.Finish Practice'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::FINISH_PRACTICE
                ];
        }

        if (isset($this->params['view_exam'])) {
            if ($exam->inProgress()) {
                $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.practices.get.viewPractice', ['practiceId' => $exam->id]),
                'label' => trans('exam.View practice'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_EXAM
            ];
            }
        }

        if (count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }


    public function includeQuestions(Exam $exam)
    {
        $student = $exam->student;

        $questions =  $exam->questions()
            ->whereHas(
                "answers",
                function (Builder $answers) use ($student) {
                    $answers->where("student_id", "=", $student->id);
                }
            )
            ->get();

        $params = [
            'is_answer' => true
        ];

        if (isset($this->params['actions'])) {
            $params['actions'] = false;
        }

        return $this->collection($questions, new QuestionTransformer($params), ResourceTypesEnums::EXAM_QUESTION);
    }

    public function includeFeedback(Exam $exam)
    {
        return $this->item($exam, new ExamFeedBackTransformer(), ResourceTypesEnums::EXAM_FEEDBACK);
    }

}
