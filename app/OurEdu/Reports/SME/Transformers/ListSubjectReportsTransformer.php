<?php


namespace App\OurEdu\Reports\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Reports\Report;
use League\Fractal\TransformerAbstract;

class ListSubjectReportsTransformer extends TransformerAbstract
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
            'endpoint_url' => buildScopeRoute('api.sme.subjects.get.subject',
                ['subjectId' => $report->reportable->id]),
            'label' => trans('subject.view subject details'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_SUBJECT_DETAILS
        ];

        if($report->whereIn('reportable_id', $report->reportable->subjectFormatSubject()->pluck('id'))->exists()){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports',
                    ['subject_id' => $report->reportable->id,
                        'reported_sections' => 'true']),
                'label' => trans('report.reported sections'),
                'method' => 'GET',
                'key' => APIActionsEnums::REPORTED_SECTION
            ];
        }

        if (count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
