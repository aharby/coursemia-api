<?php


namespace App\OurEdu\GradeColors\Admin;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class AssignGradesRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            "color_grades.*" => "exists:grade_classes,id"
        ];
    }
}
