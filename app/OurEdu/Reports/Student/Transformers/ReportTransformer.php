<?php


namespace App\OurEdu\Reports\Student\Transformers;

use App\OurEdu\Reports\Report;
use League\Fractal\TransformerAbstract;

class ReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
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
        ];
    }
}
