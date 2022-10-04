<?php

namespace App\OurEdu\Reports\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    private $params;

    protected array $defaultIncludes = [
        'actions',
        'resourceSubjectFormatSubjects'
    ];

    protected array $availableIncludes = [
        'subjectFormatSubjects',
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param SubjectFormatSubject $subjectFormatSubject
     * @return array
     */
    public function transform(SubjectFormatSubject $subjectFormatSubject)
    {
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'subject_type' => (string)$subjectFormatSubject->subject_type,
            'is_active' => (bool)$subjectFormatSubject->is_active,
            'is_editable' =>(bool)$subjectFormatSubject->is_editable,
            'list_order_key' =>$subjectFormatSubject->list_order_key,
        ];
    }

    public function includeSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $subjectFormatSubjects = $subjectFormatSubject->reportedSubjectFormatSubject();

        $subjectFormatSubjects = $subjectFormatSubjects->orderBy('list_order_key')->get();

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeResourceSubjectFormatSubjects(SubjectFormatSubject $subjectFormatSubject)
    {
        $resources = $subjectFormatSubject->resourceSubjectFormatSubject()->whereHas('reports')->orderBy('id', 'asc')->get();
        if (count($resources) > 0) {
            return $this->collection(
                $resources,
                new ResourceSubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeActions($subjectFormatSubject)
    {
        $actions = [];

        if ($subjectFormatSubject->reports()->count() && isset($this->params['details'])){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports', ['section_id' => $subjectFormatSubject->id] ),
                'label' => trans('subject.View Section Reports'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_REPORTS
            ];
        }
            if ($subjectFormatSubject->reportedSubjectFormatSubject()->count() && !isset($this->params['details'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports.sections', ['section' => $subjectFormatSubject->id]),
                'label' => trans('subject.View Section Sections'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_DETAILS
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }

    }
}
