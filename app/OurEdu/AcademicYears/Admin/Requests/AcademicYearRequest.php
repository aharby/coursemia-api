<?php


namespace App\OurEdu\AcademicYears\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class AcademicYearRequest extends BaseAppRequest
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
            'educational_system_id' => 'required|exists:educational_systems,id' ,
            'name:ar' => 'required|min:3|max:191' ,
            'name:en' => 'required|min:3|max:191' ,
            'end_date' => 'required|date'
        ];
    }

}
