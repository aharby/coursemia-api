<?php


namespace App\OurEdu\Reports\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Reports\Report;
use App\OurEdu\Reports\ReportEnum;
use League\Fractal\TransformerAbstract;

class ReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param Report $report
     * @return array
     */
    public function transform(Report $report)
    {
        // case if the reportable_type is SUBJECT
        if ($report->reportable_type == ReportEnum::SUBJECT_MODEL) {
            return [
                'id' => (int)$report->id,
                'report' => (string)$report->report,
                'report_type' => (string) ReportEnum::SUBJECT_TYPE
            ];
        }
        // case if the reportable_type is SUBJECT_FORMAT_SUBJECT
        if ($report->reportable_type == ReportEnum::SUBJECT_FORMAT_SUBJECT_MODEL) {
            return [
                'id' => (int)$report->id,
                'report' => (string)$report->report,
                'report_type' => (string) ReportEnum::SECTION_TYPE
            ];
        }

        if ($report->reportable_type == ReportEnum::RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL) {
            return [
                'id' => (int)$report->id,
                'report' => (string)$report->report,
                'report_type' => (string) ReportEnum::RESOURCE_TYPE
            ];
        }
    }

    public function includeActions(Report $report)
    {
        $actions = [];
        if (isset($this->params['subject'])) {
            $subjectId = $report->reportable->id;
            $actions[] = [
                'endpoint_url' =>  buildScopeRoute('api.sme.subjects.get.subject', ['id' => $subjectId]),
                'label' => trans('report.View Subject'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT
            ];
        }

        if (isset($this->params['subjectFormatSubject'])) {
            $subjectFormatSubjectId = $report->reportable->id;
            $actions[] = [
                'endpoint_url' =>  buildScopeRoute('api.sme.subjects.viewSubjectFormatSubjectDetails', ['sectionId' => $subjectFormatSubjectId]),
                'label' => trans('report.View Subject Section'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_DETAILS
            ];
        }

        if (isset($this->params['resourceSubjectFormatSubject'])) {
            $resourceSubjectFormatSubjectID = $report->reportable->id;
            $actions[] = [
                'endpoint_url' =>  buildScopeRoute('api.sme.subjects.get-edit-resource', ['resourceSubjectFormatId' => $resourceSubjectFormatSubjectID]),
                'label' => trans('report.View Subject Resource'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_RESOURCE_SUBJECT_DETAILS
            ];
        }
        if (count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
