<?php


namespace App\OurEdu\StaticPages\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class StaticPageRequest extends BaseAppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'slug' => 'string|unique:static_pages',
            'is_active' => 'required|boolean',
            'url' => 'nullable|string',
            'bg_image' => 'nullable|image',
            'title:ar'  => 'required|string',
            'title:en'  => 'required|string',
//            commented till fixing rich text editor: not focusable issue
//            'body:ar'  => 'required|string',
//            'body:en' => 'required|string'
        ];


        return $rules;
    }

}
