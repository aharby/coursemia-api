<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class SchoolAccountBranchRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:190',
            'school_account_id' => 'required|exists:school_accounts,id',
        ];

    }
}
