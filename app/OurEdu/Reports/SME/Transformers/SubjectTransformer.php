<?php


namespace App\OurEdu\Reports\SME\Transformers;

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
        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
        ];
    }

    public function includeActions($subject)
    {
        $actions = [];
        if ($subject->reports()->count()){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports', ['subject_id' => $subject->id]),
                'label' => trans('subject.View Subject Reports'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_REPORTS
            ];
        }
        if ($subject->reportedSubjectFormatSubject()->count() && !isset($this->params['details'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports.subject', ['subject' => $subject->id]),
                'label' => trans('subject.View Subject Sections'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_DETAILS
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSubjectFormatSubjects(Subject $subject)
    {
        $subjectFormatSubjects = $subject->reportedSubjectFormatSubject();

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
