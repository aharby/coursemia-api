<?php


namespace App\OurEdu\EducationalSystems\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class EducationalSystemRequest extends BaseAppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country_id' => 'required|exists:countries,id' ,
            'name:ar' => 'required|min:2|max:191' ,
            'name:en' => 'required|min:2|max:191' ,
        ];
    }

    public function messages()
    {
        return [
            'name.ar.required' => trans('validation.required'),
            'name.ar.min' => trans('validation.'),

        ];
    }

}
