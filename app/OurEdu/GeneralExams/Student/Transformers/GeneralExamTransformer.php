<?php

namespace App\OurEdu\GeneralExams\Student\Transformers;

use App\OurEdu\Exams\Models\Exam;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\GeneralExams\Models\GeneralExamStudent;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Exams\Student\Transformers\Practices\QuestionTransformer;

class GeneralExamTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'subject',
        'actions',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralExam $exam)
    {
        $transformerDatat = [
            'id' => (int)$exam->id,
            'name' => $exam->name,
            'date' => $exam->date,
            'start_time' => $exam->start_time,
            'end_time' => $exam->end_time,
        ];


        return $transformerDatat;
    }

    public function includeActions(GeneralExam $exam)
    {
        $actions = [];

        if ($user = Auth::guard('api')->user()) {
            if (! GeneralExamStudent::where([
                'student_id'    =>  $user->student->id,
                'general_exam_id'    =>  $exam->id,
            ])->exists()) {
                //Start Exam
                $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.general_exams.post.startExam', ['examId' => $exam->id]),
                'label' => trans('exam.Start Exam'),
                'method' => 'POST',
                'key' => APIActionsEnums::START_GENERAL_EXAM
            ];
            }
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSubject(GeneralExam $exam)
    {
        $subject = $exam->subject;

        return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
    }
}
