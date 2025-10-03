<?php

namespace App\Modules\Config\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Config\Config;
use App\Modules\Config\Repository\ConfigRepositoryInterface;
use App\Modules\Config\Requests\ConfigRequest;
use App\Modules\Countries\Resources\Api\ListAdminConfigsIndex;

class ConfigsAdminController extends Controller
{


    public function __construct(public ConfigRepositoryInterface $configRepository)
    {
    }

    public function show()
    {
        $configs = $this->configRepository->get();
        return customResponse(ListAdminConfigsIndex::collection($configs), __('Done'), 200, StatusCodesEnum::DONE);
    }

    public function update(ConfigRequest $request)
    {
        $rows = $request->all();
        if ($rows) {
            foreach ($rows as $row) {
                $rowToUpdate = Config::find($row['id']);
                if ($rowToUpdate) {
                    $rowToUpdate->update([
                        'value:en' => $row['value_en'],
                        'value:ar' => $row['value_ar'],
                    ]);
                }
            }
        }
        \Cache::forget('configs');
        updateConfigsCache();
        return customResponse('', trans('api.Updated Successfully'), 200, 1);
    }

}
