<?php

namespace App\Modules\Country\Controllers;

use App\Enums\StatusCodesEnum;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Countries\Resources\Api\ListCountriesIndex;
use App\Modules\Countries\Resources\Api\ListCountriesIndexPaginator;

class CountriesApiControllers
{
    public function __construct(
        public CountryRepositoryInterface $countryRepository
    )
    {
    }

    public function index()
    {
        $countries = Country::query()->active();
        $countries = $countries->get();
        return customResponse(ListCountriesIndex::collection($countries), __('Done'), 200, StatusCodesEnum::DONE);
    }
}
