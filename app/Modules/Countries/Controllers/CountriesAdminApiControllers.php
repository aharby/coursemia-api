<?php

namespace App\Modules\Countries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Countries\Resources\Admin\ListAdminCountriesIndexPaginator;
use Illuminate\Http\Request;

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
        return customResponse(new ListAdminCountriesIndexPaginator($countries), '', true, 200);

    }

    public function store(Request $request)
    {
        $data = [];
        if ($request->has('is_active')) {
            $data['is_active'] = $request->get('is_active');
        }
        if ($request->has('title_en')) {
            $data['title:en'] = $request->get('title_en');
        }
        if ($request->has('title_ar')) {
            $data['title:ar'] = $request->get('title_ar');
        }
        if ($request->has('country_code')) {
            $data['country_code'] = $request->get('country_code');
        }
        if ($request->has('flag')) {
            $data['flag'] = $request->get('flag');
        }
        if ($this->countryRepository->create($data)) {
            return customResponse('', trans('api.Created Successfully'), true, 200);
        }
        return customResponse('', trans('api.oops something went wrong'), false, 400);
    }

    public function update(Request $request, $id)
    {
        $data = [];
        if ($request->has('is_active')) {
            $data['is_active'] = $request->get('is_active');
        }
        if ($this->countryRepository->update($id, $data)) {
            return customResponse('', trans('api.Updated Successfully'), true, 200);
        }
        return customResponse('', trans('api.oops something went wrong'), false, 400);

    }

    public function destroy($id)
    {
        if ($this->countryRepository->delete($id)) {
            return customResponse('', trans('api.Deleted Successfully'), true, 200);
        };
        return customResponse('', trans('api.oops something went wrong'), false, 400);
    }

}
