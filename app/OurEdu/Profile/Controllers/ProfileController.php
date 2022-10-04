<?php

namespace App\OurEdu\Profile\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Users\UserEnums;

class ProfileController extends BaseController
{
    public function getLogout()
    {
        if (in_array(auth()->user()->type,[UserEnums::SUPER_ADMIN_TYPE],UserEnums::ADMIN_TYPE)){
            auth()->logout();
            return redirect()->route('auth.get.login');
        }
        auth()->logout();
        return redirect()->route('auth.get.schoolLogin');
    }
}
