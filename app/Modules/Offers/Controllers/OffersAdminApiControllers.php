<?php

namespace App\Modules\Offers\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Offers\Repository\OffersRepositoryInterface;
use App\Modules\Offers\Resources\Admin\ListAdminOffersIndex;
use Illuminate\Http\Request;

class OffersAdminApiControllers extends Controller
{
    public function __construct(
        public OffersRepositoryInterface $offersRepository
    )
    {
    }

    public function index()
    {
        $offers = $this->offersRepository->all();
        return response()->json([
            'total' => $offers->total(),
            'offers' => ListAdminOffersIndex::collection($offers->items())
        ]);
    }


    public function show($id)
    {
        $speciality = $this->offersRepository->find($id);
        if ($speciality) {
            return customResponse(new ListAdminOffersIndex($speciality), '', 200, 1);
        };
        return customResponse('', trans('api.no speciality found'), 400, 2);
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
        if ($request->has('expiration_date')) {
            $data['expiration_date'] = $request->get('expiration_date');
        }
        if ($request->has('offer_value')) {
            $data['offer_value'] = $request->get('offer_value');
        }
        if ($request->has('offer_type')) {
            $data['offer_type'] = $request->get('offer_type');
        }
        if ($request->has('offer_code')) {
            $data['offer_code'] = $request->get('offer_code');
        }

        if ($request->has('image')) {
            $data['image'] = moveSingleGarbageMedia($request->get('image'), 'specialities');
        }
        if ($this->offersRepository->create($data)) {
            return customResponse('', trans('api.Created Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

    public function update(Request $request, $id)
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
        if ($request->has('image')) {
            $data['image'] = moveSingleGarbageMedia($request->get('image'), 'specialities');
        }
        if ($request->has('offer_value')) {
            $data['offer_value'] = $request->get('offer_value');
        }
        if ($request->has('offer_type')) {
            $data['offer_type'] = $request->get('offer_type');
        }
        if ($request->has('offer_code')) {
            $data['offer_code'] = $request->get('offer_code');
        }
        if ($this->offersRepository->update($id, $data)) {
            return customResponse('', trans('api.Updated Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);

    }

    public function destroy($id)
    {
        if ($this->offersRepository->delete($id)) {
            return customResponse('', trans('api.Deleted Successfully'), 200, 1);
        };
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }
}
