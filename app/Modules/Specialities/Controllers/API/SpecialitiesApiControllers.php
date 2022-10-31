<?php

namespace App\Modules\Specialities\Controllers\API;

use App\Enums\StatusCodesEnum;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Countries\Resources\Api\ListCountriesIndexPaginator;
use App\Modules\Specialities\Models\Speciality;
use App\Modules\Specialities\Resources\API\ListAPISpecialities;

class SpecialitiesApiControllers
{

    public function index()
    {
        $specialities = Speciality::query()->active();
        $specialities = $specialities->get();
        return customResponse(ListAPISpecialities::collection($specialities), __('Done'), 200, StatusCodesEnum::DONE);
    }
}
