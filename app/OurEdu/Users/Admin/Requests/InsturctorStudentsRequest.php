<?php

namespace App\OurEdu\Users\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\UserEnums;
use Illuminate\Validation\Rule;

class InsturctorStudentsRequest extends BaseAppRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'instructedStudents'    =>  'required|array|exists:users,id'
        ];
    }

    protected function prepareForValidation()
    {
    }
}
