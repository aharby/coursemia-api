<?php

namespace App\OurEdu\Subjects\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Subjects\Models\Subject;

class SubjectRequest extends BaseAppRequest
{
    public function rules()
    {
        if($this->route('id') &&  Subject::find($this->route('id'))->is_aptitude)
        {
             return [
                    'name' => 'required|max:250',
                    'image' => 'mimes:jpeg,bmp,png',
             ];
        }

        return [
            'name' => 'required|max:250',
            'country_id' => 'required',
            'educational_system_id' => 'required',
            'grade_class_id' => 'required',
            'is_active' => 'required|boolean',
            'subscription_cost' => 'numeric|min:0|required',
            'start_date' => 'nullable|date_format:"Y-m-d"|before:end_date',
            'end_date' => 'nullable|date_format:"Y-m-d"|after:start_date',
            'image' => 'mimes:jpeg,bmp,png',
            'direction'=>'required'

        ];
    }
}
