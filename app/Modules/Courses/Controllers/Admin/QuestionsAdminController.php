<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Answer;
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
        $question = Question::find($id);
        $question->course_id = $request->course_id;
        $question->category_id = $request->category_id;
        if (isset($request->title_en))
            $question->{'title:en'} = $request->title_en;
        if ($request->title_ar){
            $question->{'title:ar'} = $request->title_ar;
        }
        if (isset($request->explanation_en))
            $question->{'explanation:en'} = $request->explanation_en;
        if ($request->explanation_ar){
            $question->{'explanation:ar'} = $request->explanation_ar;
        }
        if (isset($request->image_id) && $request->image_id != 'undefined')
            $question->image = moveSingleGarbageMediaToPublic($request->image_id, 'courses');
        if (isset($request->explanation_image_id) && $request->explanation_image_id != 'undefined')
            $question->explanation_image = moveSingleGarbageMediaToPublic($request->explanation_image_id, 'courses');
        if (isset($request->explanation_voice) && $request->explanation_voice != 'undefined')
            $question->explanation_voice = $request->explanation_voice;
        $question->is_free_content = $request->is_free_content;
        $question->is_active = $request->is_active;

        if ($question->save()) {
            foreach ($request->answers as $questionAnswer){
                $answer = Answer::find($questionAnswer['id']);
                $answer->{'answer:en'} = $questionAnswer['answer_en'];
                $answer->{'answer:ar'} = $questionAnswer['answer_ar'];
                $answer->is_correct = $questionAnswer['is_correct'];
                $answer->question_id = $question->id;
                $answer->chosen_percentage = 0;
                $answer->save();
            }
            return customResponse('', trans('api.Updated Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

    public function destroy($id){
        Question::where('id', $id)->delete();
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
        if (isset($request->questionData['explanation_en']) && $request->questionData['explanation_en'] != 'undefined')
            $question->{'explanation:en'} = $request->questionData['explanation_en'];
        if ($request->questionData['explanation_ar']){
            $question->{'explanation:ar'} = $request->questionData['explanation_ar'];
        }
        if (isset($request->questionData['image']) && $request->questionData['image'] != 'undefined')
            $question->image = moveSingleGarbageMediaToPublic($request->questionData['image'], 'courses');;
        if (isset($request->questionData['explanation_image']) && $request->questionData['explanation_image'] != 'undefined')
            $question->explanation_image = moveSingleGarbageMediaToPublic($request->questionData['explanation_image'], 'courses');
        if (isset($request->questionData['voice_url']) && $request->questionData['voice_url'] != 'undefined')
            $question->explanation_voice = $request->questionData['voice_url'];
        $question->is_free_content = $request->questionData['is_free_content'];
        $question->save();
        foreach ($request->questionData['answers'] as $questionAnswer){
            $answer = new Answer;
            $answer->{'answer:en'} = $questionAnswer['answer_en'];
            $answer->{'answer:ar'} = $questionAnswer['answer_ar'];
            $answer->is_correct = $questionAnswer['is_correct'];
            $answer->question_id = $question->id;
            $answer->chosen_percentage = 0;
            $answer->save();
        }
        return customResponse(new AdminQuestionsResource($question), "Question added successfully", 200, StatusCodesEnum::DONE);
    }

}
