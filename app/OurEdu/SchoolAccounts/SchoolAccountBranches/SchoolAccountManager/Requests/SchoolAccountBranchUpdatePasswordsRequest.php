<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class SchoolAccountBranchUpdatePasswordsRequest extends BaseAppRequest
{
    public function rules()
    {

        return [
            'password' => 'required|min:8'
        ];

    }
}
