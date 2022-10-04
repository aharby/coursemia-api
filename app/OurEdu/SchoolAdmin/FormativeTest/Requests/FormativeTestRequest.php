<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\Requests;

use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class FormativeTestRequest extends BaseAppRequest
{
    #[ArrayShape([])]
    public function rules(): array
    {
        return [
            "title" => "required",
            "grade_class_id" => "required",
            "subject_id" => "required",
            "from" => 'required|date_format:"Y-m-d|after_or_equal:today',
            "from_time" => "required",
            "to" => 'required|date_format:"Y-m-d|after_or_equal:today',
            "to_time" => "required"
  
      ];
    }
}
