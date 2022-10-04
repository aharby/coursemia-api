<?php


namespace App\OurEdu\Options\Admin\Requests;


use Illuminate\Foundation\Http\FormRequest;

class OptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title:*'=>'required',
            'is_active'=>'required|boolean',
        ];
    }
}