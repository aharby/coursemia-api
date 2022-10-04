<?php

namespace App\OurEdu\Reports\Student\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class ReportRequest extends BaseApiParserRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.report' => 'nullable'
        ];
    }
}
