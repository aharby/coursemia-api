<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class RetakePeriodicTestRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.start_at' => 'required|date_format:"Y-m-d H:i:s|before:attributes.end_at|after:' . now(),
            'attributes.end_at' => 'required|date_format:"Y-m-d H:i:s|after:attributes.start_at',
        ];
    }
}
