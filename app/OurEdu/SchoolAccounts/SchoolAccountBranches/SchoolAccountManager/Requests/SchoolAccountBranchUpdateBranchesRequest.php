<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class SchoolAccountBranchUpdateBranchesRequest extends BaseAppRequest
{
    public function rules()
    {

        return [
            'branches' => 'required|array|min:1'
        ];

    }
}
