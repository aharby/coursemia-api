<?php

namespace App\Modules\Config\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\BaseApp\Enums\ParentEnum;
use App\Modules\Config\Repository\ConfigRepositoryInterface;
use App\Modules\Config\Requests\ConfigRequest;
use App\Modules\Countries\Resources\Api\ListConfigsIndex;
use App\Modules\Users\Admin\Middleware\IsSuperAdmin;
use App\VersionConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ConfigsController extends Controller {

    public function __construct(public ConfigRepositoryInterface $configRepository)
    {
    }

    public function index()
    {
        $configs = $this->configRepository->get();
        return customResponse(ListConfigsIndex::collection($configs), __('Done'), 200, StatusCodesEnum::DONE);
    }

    public function uploadImage(Request $request)
    {
        $pdf = $request->upload;
        $fileExtension = trim($pdf->getClientOriginalExtension());
        if (!isset($fileExtension) || $fileExtension == '' || $fileExtension == '      '){
            $fileExtension = 'mp3';
        }
        $fileName = strtolower(Str::random(10).trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pdf->getClientOriginalName()), '-')) . '.' . $fileExtension;
        $location = 'public/uploads/documents';
        $path = $pdf->storeAs(
            $location, $fileName
        );
        return customResponse([
            'url' => asset('storage' . '/' . $path),
        ],'',200, 200);
    }
}
