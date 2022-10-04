<?php


namespace App\OurEdu\Reports\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Reports\Report;
use League\Fractal\TransformerAbstract;

class ListResourceReportsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];


    /**
     * @param Report $report
     * @return array
     */
    public function transform(Report $report)
    {
        return [
            'id' => (int)$report->id,
            'report' => (string)$report->report,
            'reports_count' => (int) $report->reportable->reports()->count()
        ];
    }

    public function includeActions(Report $report)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.get-edit-resource',
                ['resourceSubjectFormatId' => $report->reportable->id]),
            'label' => trans('subject.view resource details'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_RESOURCE_SUBJECT_DETAILS
        ];

        if (count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
