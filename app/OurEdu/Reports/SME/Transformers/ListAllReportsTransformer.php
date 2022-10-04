<?php


namespace App\OurEdu\Reports\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Reports\Report;
use App\OurEdu\Reports\ReportEnum;
use League\Fractal\TransformerAbstract;

class ListAllReportsTransformer extends TransformerAbstract
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
            'subject_name' => (string)$report->reportable->name,
            'reports_count' => (int) $report->reportable->reports()->count()
        ];
    }

    public function includeActions(Report $report)
    {
        $actions = [];
        // case: SUBJECT
        if ($report->reportable_type == ReportEnum::SUBJECT_MODEL) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports',
                        ['subject_id' => $report->reportable->id]),
                'label' => trans('subject.view subject reports'),
                'method' => 'GET',
                'key' => APIActionsEnums::SUBJECT_REPORTS
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
        }

        // case: SUBJECT_FORMAT_SUBJECT
        if ($report->reportable_type == ReportEnum::SUBJECT_FORMAT_SUBJECT_MODEL) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.subjects.viewSubjectFormatSubjectDetails',
                    ['sectionId' => $report->reportable->id]),
                'label' => trans('subject.View Section Details'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_FORMAT_SUBJECT_DETAILS
            ];

            // reports on the section
            if($report->whereIn('reportable_id', $report->reportable->resourceSubjectFormatSubject()->pluck('id'))->exists()){
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports',
                        ['section_id' => $report->reportable->id]),
                    'label' => trans('report.reported resources'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::REPORTED_RESOURCE
                ];
            }

            // reported resources under this section
            if($report->whereIn('reportable_id', $report->reportable->resourceSubjectFormatSubject()->pluck('id'))->exists()){
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports',
                        ['section_id' => $report->reportable->id]),
                    'label' => trans('report.reported resources'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::REPORTED_RESOURCE
                ];
            }
        }

        // case RESOURCE_SUBJECT_FORMAT_SUBJECT
        if ($report->reportable_type == ReportEnum::RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.subjects.get-edit-resource',
                    ['resourceSubjectFormatId' => $report->reportable->id]),
                'label' => trans('subject.view resource details'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_RESOURCE_SUBJECT_DETAILS
            ];
        }

        if (count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
