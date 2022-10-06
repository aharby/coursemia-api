<?php

namespace App\Modules\Countries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Countries\Models\Country;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Countries\Resources\ListAdminCountriesIndex;
use App\Modules\Countries\Resources\ListAdminCountriesIndexPaginator;

class CountriesAdminApiControllers extends Controller
{
    public function __construct(
        public CountryRepositoryInterface $countryRepository
    )
    {
    }

    public function index()
    {
        $countries = $this->countryRepository->all();
        return customResponse(new ListAdminCountriesIndexPaginator($countries), __('Logged in successfully'), true, 200);

    }

}
