<?php


namespace App\OurEdu\Certificates\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class ThankingCertificatesRequest extends BaseAppRequest
{
    public function rules()
    {
        $rules = [
            "image" => "image",
            "attributes" => "required|array",
            "attributes.*" => "required|array",
            "attributes.name.*" => "required",
            "attributes.teacher.*" => "required",
            "attributes.subject.*" => "required",
        ];

        if ($this->isMethod("post")) {
            $rules["image"] = "required|image";
        }

        return $rules;
    }
}
