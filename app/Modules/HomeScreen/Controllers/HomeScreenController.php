<?php

namespace App\Modules\HomeScreen\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\Question;
use App\Modules\Courses\Resources\API\CoursesResource;
use App\Modules\Courses\Resources\API\QuestionResource;
use App\Modules\Courses\Resources\API\SpecialitiesResource;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Resources\API\EventsResource;
use App\Modules\Offers\Models\Offer;
use App\Modules\Offers\Resources\API\OffersResource;
use App\Modules\Specialities\Models\Speciality;

class HomeScreenController extends Controller
{
    public function getHomeScreen(){
        $offers = Offer::get();
        $events = Event::get();
        $question = Question::inRandomOrder()->first();
        $courses = Course::take(5)->get();
        $specialities = Speciality::get();
        return customResponse([
            "offers" => OffersResource::collection($offers),
            "events" => EventsResource::collection($events),
            "question_of_the_day"   => new QuestionResource($question),
            "specialities"          => [
                SpecialitiesResource::collection($specialities),
            ],
            "courses"   => CoursesResource::collection($courses)
        ], __("Get home content successfully"), 200, StatusCodesEnum::DONE);
    }
}
