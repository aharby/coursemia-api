<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class SetRoleRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'role_id' => 'required'
        ];
    }

}
