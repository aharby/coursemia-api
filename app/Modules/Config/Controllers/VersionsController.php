<?php

namespace App\Modules\Config\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\BaseApp\Enums\ParentEnum;
use App\Modules\Config\Config;
use App\Modules\Config\Repository\ConfigRepositoryInterface;
use App\Modules\Config\Requests\ConfigRequest;
use App\Modules\Users\Admin\Middleware\IsSuperAdmin;
use App\VersionConfig;
use Intervention\Image\Facades\Image;

class VersionsController extends Controller {

    public function getVersions(){
        $versions = Config::whereIn('field', ['android_version', 'ios_version'])->get();
        foreach ($versions as $mobileVersion){
            $version['ios_version'] = (integer)$mobileVersion->translate('en')->value;
            $version['android_version'] = (integer)$mobileVersion->translate('en')->value;
        }
        return customResponse($version, "", 200, StatusCodesEnum::DONE);
    }

}
