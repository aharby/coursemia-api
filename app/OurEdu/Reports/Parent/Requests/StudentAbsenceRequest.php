<?php


namespace App\OurEdu\Reports\Parent\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class StudentAbsenceRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            "student_id" => "required|exists:users,id",
            "from" => "date|before:now",
            "to" => "date|before:now",
            "subject_id" => "",
        ];
    }
}
