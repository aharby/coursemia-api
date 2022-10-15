<?php

namespace App\Modules\Config\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\BaseApp\Enums\ParentEnum;
use App\Modules\Config\Repository\ConfigRepositoryInterface;
use App\Modules\Config\Requests\ConfigRequest;
use App\Modules\Users\Admin\Middleware\IsSuperAdmin;
use App\VersionConfig;
use Intervention\Image\Facades\Image;

class VersionsController extends Controller {

    public function getVersions(){
        $version = VersionConfig::latest()->first();
        return customResponse([
            "ios_version"   => (integer)$version->ios_version ?? 0,
            "android_version"   => (integer)$version->android_version ?? 0
        ], "", 200, StatusCodesEnum::DONE);
    }

}
