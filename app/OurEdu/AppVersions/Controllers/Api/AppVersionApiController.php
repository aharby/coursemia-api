<?php

namespace App\OurEdu\AppVersions\Controllers\Api;

use App\OurEdu\AppVersions\Repository\AppVersionRepositoryInterface;
use App\OurEdu\AppVersions\Transformers\AppVersionTransformer;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

class AppVersionApiController extends BaseApiController
{
    use ApiResponser;

    /**
     * @var AppVersionRepositoryInterface
     */
    private $appVersionRepo;

    public function __construct(AppVersionRepositoryInterface $appVersionRepInt)
    {
        $this->appVersionRepo = $appVersionRepInt;
    }

    public function getVersions()
    {
        $rows = $this->appVersionRepo->getByName(request()->get('name'));

        return $this->transformDataMod($rows, new AppVersionTransformer(), ResourceTypesEnums::APP_VERSION);
    }
}
