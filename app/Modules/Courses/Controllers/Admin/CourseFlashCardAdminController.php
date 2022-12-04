<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Repository\FlashCardRepositoryInterface;
use App\Modules\Courses\Resources\Admin\FlashCardsAdminResource;
use App\Modules\Courses\Resources\API\AdminCourseNoteResource;
use Illuminate\Http\Request;

class CourseFlashCardAdminController extends Controller
{
    public function __construct(
        public FlashCardRepositoryInterface $flashCardRepository
    )
    {
    }

    public function index()
    {
        $flashCards = $this->flashCardRepository->all();

        return response()->json([
            'total' => $flashCards->total(),
            'flashCards' => FlashCardsAdminResource::collection($flashCards->items())
        ]);
    }

    public function show($id)
    {
        $flashCard = $this->flashCardRepository->find($id);
        if ($flashCard) {
            return customResponse(new FlashCardsAdminResource($flashCard), '', 200, 1);
        };
        return customResponse('', trans('api.no flash card found found'), 404, 2);
    }

    public function update(Request $request, $id)
    {
        $data = [];
        if ($request->has('is_active')) {
            $data['is_active'] = $request->get('is_active');
        }
        if ($request->has('front_en')) {
            $data['front:en'] = $request->get('front_en');
        }
        if ($request->has('front_ar')) {
            $data['front:ar'] = $request->get('front_ar');
        }
        if ($request->has('back_en')) {
            $data['back:en'] = $request->get('back_en');
        }
        if ($request->has('back_ar')) {
            $data['back:ar'] = $request->get('back_ar');
        }
        if ($request->has('category_id')) {
            $data['category_id'] = $request->get('category_id');
        }
        if ($request->has('course_id')) {
            $data['course_id'] = $request->get('course_id');
        }
        if ($request->has('is_free_content')) {
            $data['is_free_content'] = $request->get('is_free_content');
        }
        if ($this->flashCardRepository->update($id, $data)) {
            return customResponse('', trans('api.Updated Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

    public function destroy($id)
    {
        if ($this->flashCardRepository->delete($id)) {
            return customResponse('', trans('api.Deleted Successfully'), 200, 1);
        };
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

    public function store(Request $request)
    {
        if ($request->has('is_active')) {
            $data['is_active'] = $request->get('is_active');
        }
        if ($request->has('front_en')) {
            $data['front:en'] = $request->get('front_en');
        }
        if ($request->has('front_ar')) {
            $data['front:ar'] = $request->get('front_ar');
        }
        if ($request->has('back_en')) {
            $data['back:en'] = $request->get('back_en');
        }
        if ($request->has('back_ar')) {
            $data['back:ar'] = $request->get('back_ar');
        }
        if ($request->has('category_id')) {
            $data['category_id'] = $request->get('sub_category_id') ?? $request->get('category_id');
        }
        if ($request->has('course_id')) {
            $data['course_id'] = $request->get('course_id');
        }
        if ($request->has('is_free_content')) {
            $data['is_free_content'] = $request->get('is_free_content');
        }
        if ($this->flashCardRepository->create($data)) {
            return customResponse('', trans('api.Created Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);

    }

}
