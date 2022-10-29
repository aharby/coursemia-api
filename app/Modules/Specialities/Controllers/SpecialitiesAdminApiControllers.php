<?php

namespace App\Modules\Specialities\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Specialities\Repository\SpecialitiesRepositoryInterface;
use App\Modules\Specialities\Resources\Admin\ListAdminSpecialitiesIndex;
use App\Modules\Specialities\Resources\Admin\ListAdminSpecialitiesIndexPaginator;
use Illuminate\Http\Request;

class SpecialitiesAdminApiControllers extends Controller
{
    public function __construct(
        public SpecialitiesRepositoryInterface $specialitiesRepository
    )
    {
    }

    public function index()
    {
        $specialities = $this->specialitiesRepository->all();
        return response()->json([
            'total' => $specialities->total(),
            'specialities' => ListAdminSpecialitiesIndex::collection($specialities->items())
        ]);
    }

    public function show($id)
    {
        $speciality = $this->specialitiesRepository->find($id);
        if ($speciality) {
            return customResponse(new ListAdminSpecialitiesIndex($speciality), '', 200,1);
        };
        return customResponse('', trans('api.no speciality found'), 400,2);
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

        if ($request->has('image')) {
            $data['image'] = moveSingleGarbageMedia($request->get('image'), 'specialities');
        }
        if ($this->specialitiesRepository->create($data)) {
            return customResponse('', trans('api.Created Successfully'), 200,1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400,2);
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
        if ($this->specialitiesRepository->update($id, $data)) {
            return customResponse('', trans('api.Updated Successfully'), 200,1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400,2);

    }

    public function destroy($id)
    {
        if ($this->specialitiesRepository->delete($id)) {
            return customResponse('', trans('api.Deleted Successfully'),  200,1);
        };
        return customResponse('', trans('api.oops something went wrong'),  400,2);
    }
}
