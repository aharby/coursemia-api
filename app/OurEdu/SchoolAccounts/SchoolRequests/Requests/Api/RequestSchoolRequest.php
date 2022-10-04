<?php

namespace App\OurEdu\SchoolAccounts\SchoolRequests\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class RequestSchoolRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.school_name' => 'required|max:190',
            'attributes.number_of_students' => 'required|min:0|integer',
            'attributes.manager_name' => 'required|max:190',
            'attributes.manager_mobile' => 'required',
            'attributes.manager_email' => 'required|email',
        ];
    }
}
