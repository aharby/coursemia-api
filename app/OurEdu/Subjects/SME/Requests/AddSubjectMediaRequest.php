<?php

namespace App\OurEdu\Subjects\SME\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class AddSubjectMediaRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.media' => 'required|array',
            'attributes.media.*' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf,xls,csv,txt,xlsx|max:2048',
        ];
    }
}
