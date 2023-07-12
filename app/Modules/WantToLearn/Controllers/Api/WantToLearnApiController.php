<?php

namespace App\Modules\WantToLearn\Controllers\Api;

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
use App\Modules\WantToLearn\Models\WantToLearn;
use App\Modules\WantToLearn\Requests\AddWantToLearnRequest;
use App\Modules\WantToLearn\Resources\WantToLearnCollection;
use Illuminate\Http\Request;

class WantToLearnApiController extends Controller
{
    public function getMyWantToLearn()
    {
        $user = auth('api')->user();
        $wantToLearn = WantToLearn::where('user_id', $user->id)
            ->paginate(request()->perPage, ['*'], 'page', request()->page);
        return customResponse(new WantToLearnCollection($wantToLearn), 'Done', 200, StatusCodesEnum::DONE);
    }

    public function addWantToLearn(AddWantToLearnRequest $request)
    {
        $user = auth('api')->user();
        $wantToLearn = WantToLearn::firstOrCreate([
                'lecture_id' => $request->lecture_id,
                'user_id'   => $user->id
            ]);
        return customResponse((object)[], 'Added Successfully', 200, StatusCodesEnum::DONE);
    }

    public function deleteWantToLearn($id)
    {
        $user = auth('api')->user();
        $wantToLearn = WantToLearn::where(['id' => $id, 'user_id' => $user->id])->first();
        if (isset($wantToLearn)){
            $wantToLearn->delete();
            return customResponse((object)[], 'Deleted Successfully', 200, StatusCodesEnum::DONE);
        }else{
            return customResponse((object)[], 'Not Found', 404, StatusCodesEnum::FAILED);
        }
    }
}
