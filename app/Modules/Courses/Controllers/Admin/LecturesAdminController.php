<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Repository\LectureRepositoryInterface;
use App\Modules\Courses\Resources\Admin\LecturesResource;
use Illuminate\Http\Request;

class LecturesAdminController extends Controller
{
    public function __construct(
        public LectureRepositoryInterface $lectureRepository
    )
    {
    }
    public function index(Request $request){
        $lectures = CourseLecture::query();
        if (isset($request->course)){
            $lectures = $lectures->where('course_id', $request->course);
        }

        $lectures = $lectures->sorter();
        $lectures = $lectures->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $lectures->total(),
            'lectures' => LecturesResource::collection($lectures->items())
        ]);
    }

    public function show($id){
        $lecture = CourseLecture::find($id);
        return customResponse(new LecturesResource($lecture), "Done", 200, StatusCodesEnum::DONE);
    }

    public function update(Request $request, $id){
        $data = [];
        if ($request->has('is_active')) {
            $data['is_active'] = $request->get('is_active');
        }
        if ($request->has('is_free_content')) {
            $data['is_free_content'] = $request->get('is_free_content');
        }
        if ($request->has('title_en')) {
            $data['title_en'] = $request->get('title_en');
        }
        if ($request->has('title_ar')) {
            $data['title_ar'] = $request->get('title_ar');
        }
        if ($request->has('description_en')) {
            $data['description_en'] = $request->get('description_en');
        }
        if ($request->has('description_en')) {
            $data['description_ar'] = $request->get('description_en');
        }
        if ($request->has('category_id')) {
            $data['category_id'] = $request->get('category_id');
        }
        if ($request->has('course_id')) {
            $data['course_id'] = $request->get('course_id');
        }
        if ($request->has('url')) {
            $data['url'] = $request->get('url');
        }
        if ($request->has('image')) {
            $data['video_thumb'] = moveSingleGarbageMediaToPublic($request->get('image'), 'lectures');
        }
        if ($this->lectureRepository->update($id, $data)) {
            return customResponse('', trans('api.Updated Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

    public function destroy($id){
        Course::where('id', $id)->delete();
        return customResponse(null, "Deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function store(Request $request){
        $lecture = new CourseLecture();
//        if ($request->has('is_active')){
//            $lecture->is_active = $request->is_active;
//        }
        $lecture->course_id = $request->lectureData['course_id'];
        $lecture->category_id = $request->lectureData['category_id'];
        $lecture->url = $request->lectureData['path'];
        $lecture->title_en = $request->lectureData['title_en'];
        if ($request->lectureData['title_ar']){
            $lecture->title_ar = $request->lectureData['title_ar'];
        }
        $lecture->description_en = $request->lectureData['description_en'];

        if (isset($request->lectureData['description_ar'])){
            $lecture->description_ar = $request->lectureData['description_ar'];
        }
        $lecture->is_free_content = $request->lectureData['is_free_content'];

        if (isset($request->lectureData['video_thumb'])){
            $lecture->video_thumb = moveSingleGarbageMediaToPublic($request->lectureData['video_thumb'], 'courses');
        }
        $lecture->save();
        return customResponse(new LecturesResource($lecture), "Lecture added successfully", 200, StatusCodesEnum::DONE);
    }


    public function delete($id){
        CourseLecture::where('id', $id)->delete();
        return customResponse((object)[], "Lecture deleted successfully", 200, StatusCodesEnum::DONE);
    }
}
