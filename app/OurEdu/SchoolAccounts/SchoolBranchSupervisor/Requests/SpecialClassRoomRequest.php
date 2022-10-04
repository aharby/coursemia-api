<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Validation\Rule;

class SpecialClassRoomRequest extends BaseAppRequest
{

    public function rules()
    {
        return [
            'students' => 'required|array',
            'name' => 'required|max:200',
            'grade_class_id' => 'required|exists:grade_classes,id',
            'educational_system_id' => 'required|exists:educational_systems,id',
            'academic_year_id' => ['required',Rule::exists('options', 'id')->where(function ($query) {
                $query->where('type', OptionsTypes::ACADEMIC_YEAR);
            })],
            'educational_term_id' => ['required',Rule::exists('options', 'id')->where(function ($query) {
                $query->where('type', OptionsTypes::EDUCATIONAL_TERM);
            })],
        ];
    }

}
