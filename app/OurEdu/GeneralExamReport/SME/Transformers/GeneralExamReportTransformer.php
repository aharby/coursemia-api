<?php

namespace App\OurEdu\GeneralExamReport\SME\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralExamReport\Models\GeneralExamReport;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralExams\Models\GeneralExamOption;

class GeneralExamReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions'
    ];

    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralExamReport $report)
    {
        $transformerData = [
            'id' => (int) $report->id,
            'general_exam_id' => (int) $report->general_exam_id,
        ];

        return $transformerData;
    }

    public function includeQuestions(GeneralExamReport $report){

        if ($report->reportQuestion()->count()){
            return $this->collection($report->reportQuestion, new GeneralExamReportQuestionsTransformer(), ResourceTypesEnums::GENERAL_EXAM_REPORT_QUESTION);
        }
    }
}
