<?php

namespace App\Modules\Config\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\BaseApp\Enums\ParentEnum;
use App\Modules\Config\Repository\ConfigRepositoryInterface;
use App\Modules\Config\Requests\ConfigRequest;
use App\Modules\Users\Admin\Middleware\IsSuperAdmin;
use App\VersionConfig;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class AdminConfigVersionsController extends Controller {

    public function show(){
        $version = VersionConfig::latest()->first();
        return customResponse([
            "ios_version"   => $version->ios_version ?? 0,
            "android_version"   => $version->android_version ?? 0
        ], "", 200, StatusCodesEnum::DONE);
    }

    public function update(Request $request){
        $version = VersionConfig::latest()->first();
        $version->android_version = $request->android_version;
        $version->ios_version = $request->ios_version;
        $version->save();
    }

}
