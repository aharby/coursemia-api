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
}
