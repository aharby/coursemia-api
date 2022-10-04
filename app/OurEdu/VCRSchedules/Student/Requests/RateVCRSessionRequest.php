<?php

namespace App\OurEdu\VCRSchedules\Student\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class RateVCRSessionRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'attributes.comment' => ['required', 'max:150'],
        ];
    }
}
