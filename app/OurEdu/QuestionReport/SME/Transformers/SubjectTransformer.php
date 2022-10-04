<?php


namespace App\OurEdu\QuestionReport\SME\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class SubjectTransformer extends TransformerAbstract
{
    protected $params;

    protected array $defaultIncludes = [
        'actions',
    ];

    protected array $availableIncludes = [
        'subjectFormatSubjects'
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Subject $subject)
    {
        if (isset($this->params['inside_general_exam'])) {
            return [
                'id' => (int)$subject->id,
                'name' => (string)$subject->name,
            ];
        }
        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'no_of_question_reports' => $subject->questionReport()->count()
        ];
    }

    public function includeActions($subject)
    {
        $actions = [];
        if (!isset($this->params['details'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.question.report.get.subject', ['subject' => $subject->id]),
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
        $subjectFormatSubjects = $subject->questionReportSubjectFormatSubject();

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
