<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Validation\Rule;

class SchoolAccountRequest extends BaseAppRequest
{
    public function rules()
    {
        $attributes = [
            'name' => 'required|max:190',
            'logo' => 'required|image',
            'manager_id' => [
                'required', Rule::unique('users', 'username')->where(function ($query) {

                    return $query->where('deleted_at', null);
                }),
            ],
            'country_id' => 'required|exists:countries,id',
            'educational_systems' => 'required|array',
            'educational_systems.*' => 'required|exists:educational_systems,id',
            'grade_classes' => 'required|array',
            'grade_classes.*' => 'required|exists:grade_classes,id',
            'academical_years' => 'required|array',
            'academical_years.*' => ['required', Rule::exists('options', 'id')->where(function ($query) {
                $query->where('type', OptionsTypes::ACADEMIC_YEAR);
            })],
            'educational_terms' => 'required|array',
            'educational_terms.*' => ['required', Rule::exists('options', 'id')->where(function ($query) {
                $query->where('type', OptionsTypes::EDUCATIONAL_TERM);
            })
            ],

        ];
        if ($this->route('id')) {
            $attributes['logo'] = 'image';
            $attributes['manager_email'] = '';
        }
        return $attributes;
    }
}
