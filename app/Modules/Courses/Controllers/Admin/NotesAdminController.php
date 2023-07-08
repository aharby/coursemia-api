<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Repository\LectureRepositoryInterface;
use App\Modules\Courses\Repository\NoteRepositoryInterface;
use App\Modules\Courses\Resources\Admin\LecturesResource;
use App\Modules\Courses\Resources\API\AdminCourseNoteResource;
use Illuminate\Http\Request;

class NotesAdminController extends Controller
{
    public function __construct(
        public NoteRepositoryInterface $noteRepository
    )
    {
    }
    public function index(Request $request){
        $notes = CourseNote::query();
        if (auth()->user()->role != 'super')
            $notes = $notes->where('admin_id', request()->header('Admin-Id'));
        if (isset($request->course)){
            $notes = $notes->where('course_id', $request->course);
        }
        if (isset($request->category) && !isset($request->sub_category)){
            $notes = $notes->where(function ($q){
                $q->where('category_id', request()->category)
                    ->orWhereHas('category', function ($cat){
                        $cat->whereHas('parent', function ($parent){
                            $parent->where('id', request()->category);
                        });
                    });
            });
        }
        if (isset($request->sub_category)){
            $notes = $notes->where('category_id', $request->sub_category);
        }

        $notes = $notes->filter()->sorter();
        $notes = $notes->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $notes->total(),
            'notes' => AdminCourseNoteResource::collection($notes->items())
        ]);
    }

    public function show($id){
        $lecture = CourseNote::find($id);
        return customResponse(new AdminCourseNoteResource($lecture), "Done", 200, StatusCodesEnum::DONE);
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
            $data['title:en'] = $request->get('title_en');
        }
        if ($request->has('title_ar')) {
            $data['title:ar'] = $request->get('title_ar');
        }
        if ($request->has('category_id')) {
            $data['category_id'] = $request->get('sub_category_id') ?? $request->get('category_id');
        }
        if ($request->has('course_id')) {
            $data['course_id'] = $request->get('course_id');
        }
        if ($request->has('url')) {
            $data['url'] = $request->get('url');
        }
        if ($this->noteRepository->update($id, $data)) {
            return customResponse('', trans('api.Updated Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

    public function destroy($id){
        CourseNote::where('id', $id)->delete();
        return customResponse(null, "Deleted successfully", 200, StatusCodesEnum::DONE);
    }

    public function store(Request $request){
        $note = new CourseNote();
//        if ($request->has('is_active')){
//            $note->is_active = $request->is_active;
//        }
        $note->course_id = $request->noteData['course_id'];
        $note->category_id = $request->noteData['sub_category_id'] ?? $request->noteData['category_id'];
        $note->url = $request->noteData['path'];
        $note->{'title:en'} = $request->noteData['title_en'];
        $note->{'title:ar'} = $request->noteData['title_ar'];
        $note->is_free_content = $request->noteData['is_free_content'];
        $note->admin_id = auth('admin')->user()->id;
        $note->save();
        return customResponse(new AdminCourseNoteResource($note), "Note added successfully", 200, StatusCodesEnum::DONE);
    }

}
