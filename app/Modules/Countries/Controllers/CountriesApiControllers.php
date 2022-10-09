<?php

namespace App\Modules\Country\Controllers;

use App\Modules\Countries\Repository\CountryRepositoryInterface;
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
        $countries = $this->countryRepository->all(true);
        return customResponse(new ListCountriesIndexPaginator($countries), '', true, 200);
    }
}
