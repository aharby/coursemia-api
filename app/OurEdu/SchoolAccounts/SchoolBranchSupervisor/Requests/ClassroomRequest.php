<?php
namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Validation\Rule;

class ClassroomRequest extends BaseAppRequest
{
    public function rules()
    {
        $attributes =  [
            'name' => 'required|max:200',
            'grade_class_id' => 'required|exists:grade_classes,id',
            'educational_system_id' => 'required|exists:educational_systems,id',
            'file' => 'required|mimeTypes:' .
                'application/vnd.ms-office,' .
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,' .
                'application/vnd.ms-excel,'.
                'application/vnd.openxmlformats-officedocument.spreadsheetml.template,'.
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheetapplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'academic_year_id' => ['required',Rule::exists('options', 'id')->where(function ($query) {
                $query->where('type', OptionsTypes::ACADEMIC_YEAR);
            })],
            'educational_term_id' => ['required',Rule::exists('options', 'id')->where(function ($query) {
                $query->where('type', OptionsTypes::EDUCATIONAL_TERM);
            })],
        ];

        if ($this->route('id')) {
            $attributes = [
                'name' => 'required|max:200',
                'file' => 'nullable|mimeTypes:' .
                    'application/vnd.ms-office,' .
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,' .
                    'application/vnd.ms-excel,'.
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.template,'.
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheetapplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];
        }

        return $attributes;

    }
}
