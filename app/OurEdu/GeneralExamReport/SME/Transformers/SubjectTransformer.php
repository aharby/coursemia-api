<?php


namespace App\OurEdu\GeneralExamReport\SME\Transformers;

use App\OurEdu\GeneralExamReport\Models\GeneralExamReport;
use App\OurEdu\GeneralExamReport\Models\GeneralExamReportQuestion;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;

class SubjectTransformer extends TransformerAbstract
{
    protected $params;


    protected array $availableIncludes = [
        'actions',
        'subjectFormatSubjects'
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Subject $subject)
    {


        $count=    GeneralExamReportQuestion::whereHas('generalExam',function ($q) use ($subject)
        {$q->where('subject_id',$subject->id);
        }) ->where('preference_parameter' , '<=' ,0)->notReported()->notIgnored()->count();

//

        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'question_count' => (int)$count,
        ];
    }

    public function includeActions($subject)
    {
        $actions = [];
        if (!isset($this->params['details'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams_reports.get.listSubjectReportedQuestions', ['subject' => $subject->id]),
                'label' => trans('subject.View Subject Sections'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_QUESTION_REPORT
            ];
        }
        if (count($actions))
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeSubjectFormatSubjects(Subject $subject)
    {
        $subjectFormatSubjects = $subject->generalExamQuestionReportSubjectFormatSubject();

        $subjectFormatSubjectsData = $subjectFormatSubjects->orderBy('list_order_key')->get();

        if (count($subjectFormatSubjectsData)) {
            return $this->collection(
                $subjectFormatSubjectsData,
                new SubjectFormatSubjectTransformer(),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
