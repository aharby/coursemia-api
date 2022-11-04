<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Models\Question;
use App\Modules\Courses\Repository\LectureRepositoryInterface;
use App\Modules\Courses\Repository\NoteRepositoryInterface;
use App\Modules\Courses\Resources\Admin\LecturesResource;
use App\Modules\Courses\Resources\API\AdminCourseNoteResource;
use App\Modules\Courses\Resources\API\AdminQuestionsResource;
use Illuminate\Http\Request;

class QuestionsAdminController extends Controller
{
    public function __construct(
        public NoteRepositoryInterface $noteRepository
    )
    {
    }
    public function index(Request $request){
        $questions = Question::query();
        if (isset($request->course)){
            $questions = $questions->where('course_id', $request->course);
        }

        $questions = $questions->filter()->sorter();
        $questions = $questions->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $questions->total(),
            'questions' => AdminQuestionsResource::collection($questions->items())
        ]);
    }

    public function show($id){
        $question = Question::find($id);
        return customResponse(new AdminQuestionsResource($question), "Done", 200, StatusCodesEnum::DONE);
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
            $data['category_id'] = $request->get('category_id');
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
        $question = new Question();
        $question->course_id = $request->questionData['course_id'];
        $question->category_id = $request->questionData['category_id'];
        $question->{'title:en'} = $request->questionData['title_en'];
        if ($request->questionData['title_ar']){
            $question->{'title:ar'} = $request->questionData['title_ar'];
        }
        $question->{'description:en'} = $request->questionData['description_en'];
        if ($request->questionData['description_ar']){
            $question->{'description:ar'} = $request->questionData['description_ar'];
        }
        $question->{'explanation:en'} = $request->questionData['explanation_en'];
        if ($request->questionData['explanation_ar']){
            $question->{'explanation:ar'} = $request->questionData['explanation_ar'];
        }
        $question->image = moveSingleGarbageMediaToPublic($request->questionData['image'], 'courses');;
        $question->explanation_image = moveSingleGarbageMediaToPublic($request->questionData['explanation_image'], 'courses');;
        $question->explanation_voice = $request->questionData['explanation_voice'];
        $question->is_free_content = $request->questionData['is_free_content'];
        $question->save();
        return customResponse(new AdminQuestionsResource($question), "Question added successfully", 200, StatusCodesEnum::DONE);
    }

}
