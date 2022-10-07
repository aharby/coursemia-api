<?php

namespace App\Modules\Countries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Countries\Resources\Admin\ListAdminCountriesIndexPaginator;

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
        return customResponse(new ListAdminCountriesIndexPaginator($countries),'', true, 200);

    }

    public function destroy($id)
    {
        if ($this->countryRepository->delete($id)) {
            return customResponse('', trans('api.Deleted Successfully'), true, 200);
        };
        return customResponse('', trans('api.oops something went wrong'), false, 400);
    }

}
