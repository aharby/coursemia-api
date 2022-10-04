<?php

namespace App\OurEdu\Feedbacks\Student\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class FeedbackRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.feedback' => 'required',
        ];
    }
}
