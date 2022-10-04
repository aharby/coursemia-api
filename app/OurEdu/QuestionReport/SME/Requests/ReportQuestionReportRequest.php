<?php

namespace App\OurEdu\QuestionReport\SME\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class ReportQuestionReportRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.note' => 'required',
            'attributes.due_date' => 'required',
        ];
    }
}
