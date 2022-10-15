<?php

namespace App\Modules\Courses\Controllers\API;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Countries\Resources\Api\ListCountriesIndexPaginator;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseLecture;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Repository\QuestionsRepositoryInterface;
use App\Modules\Courses\Resources\API\CourseDetailsResource;
use App\Modules\Courses\Resources\API\CourseLectureResource;
use App\Modules\Courses\Resources\API\CourseNoteResource;
use App\Modules\Courses\Resources\API\CoursesCollection;
use App\Modules\Courses\Resources\API\CoursesResource;
use App\Modules\Courses\Resources\Api\ListCourseQuestionsPaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoursesQuestionsAPIController extends Controller
{
    public function __construct(
        public QuestionsRepositoryInterface $questionsRepository
    )
    {
    }

    public function getCourseQuestions($courseId)
    {
        $questions = $this->questionsRepository->getQuestionsByCourseId($courseId);
        return customResponse(new ListCourseQuestionsPaginator($questions), trans('api.course questions'), 200, 1);
    }
}
