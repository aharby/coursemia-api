<?php


namespace App\OurEdu\Countries\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends BaseAppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name:ar' => 'required|min:3|max:191' ,
            'name:en' => 'required|min:3|max:191' ,
            'currency:ar' => 'required|min:3|max:191' ,
            'currency:en' => 'required|min:3|max:191' ,
            'country_code' => [
                'required',
                'max:191',
                Rule::unique('countries')->where(function ($query) {
                    return $query->where('deleted_at', null);
                })->ignore($this->route('id')),
            ],
        
        ];
    }

}
