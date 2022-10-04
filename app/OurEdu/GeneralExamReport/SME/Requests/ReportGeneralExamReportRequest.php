<?php

namespace App\OurEdu\GeneralExamReport\SME\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class ReportGeneralExamReportRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.note' => 'required',
            'attributes.due_date' => 'required',
        ];
    }
}
