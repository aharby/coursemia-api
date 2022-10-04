<?php


namespace App\OurEdu\Assessments\AssessmentManager\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class GeneralCommentRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            "attributes.comment" => "required|string"
        ];
    }
}
