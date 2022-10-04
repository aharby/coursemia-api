<?php


namespace App\OurEdu\Reports\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Reports\Report;
use League\Fractal\TransformerAbstract;

class ListSectionReportedResourcesTransformer extends TransformerAbstract
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
            'title' => (string)$report->reportable->title,
            'reports_count' => (int) $report->reportable->reports()->count()
        ];
    }

    public function includeActions(Report $report)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports',
                ['resource_id' => $report->reportable->id]),
            'label' => trans('report.View Resource Reports'),
            'method' => 'GET',
            'key' => APIActionsEnums::REPORTED_RESOURCE
        ];


        if (count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
