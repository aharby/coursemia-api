<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class AssignGradeClassRequest extends BaseAppRequest
{
    public function rules()
    {

        return [
            'branch_id' => 'required|exists:school_account_branches,id',
            'educational_system_id' => 'required|exists:educational_systems,id',
            'grade_classes' => 'array',
            'grade_classes.*' => 'exists:grade_classes,id',

        ];

    }
}
