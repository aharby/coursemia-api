<?php

namespace App\Modules\Courses\Controllers\API;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Countries\Resources\Api\ListCountriesIndexPaginator;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseFlashcard;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Repository\QuestionsRepositoryInterface;
use App\Modules\Courses\Resources\API\CourseDetailsResource;
use App\Modules\Courses\Resources\API\CourseLectureResource;
use App\Modules\Courses\Resources\API\CourseNoteResource;
use App\Modules\Courses\Resources\API\CoursesCollection;
use App\Modules\Courses\Resources\API\CoursesResource;
use App\Modules\Courses\Resources\API\FlashCardsResource;
use App\Modules\Courses\Resources\Api\ListCourseQuestionsPaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoursesFlashCardsAPIController extends Controller
{
    public function getCourseFlashCards(Request $request)
    {
        $category_id = \request()->category_id;
        $flashs = CourseFlashcard::query();
        $flashs->where('course_id', $request->course_id);
        if (isset($category_id)){
            $flashs->where('category_id', $category_id);
        }
        $flash_cards = $flashs->get();
        return customResponse(FlashCardsResource::collection($flash_cards), trans('api.course flashcards'), 200, StatusCodesEnum::DONE);
    }
}
