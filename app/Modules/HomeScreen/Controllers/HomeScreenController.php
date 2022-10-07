<?php

namespace App\Modules\HomeScreen\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Resources\API\EventsResource;
use App\Modules\Offers\Models\Offer;
use App\Modules\Offers\Resources\API\OffersResource;
use App\Modules\Questions\Models\Question;
use App\Modules\Questions\Resources\API\QuestionResource;

class HomeScreenController extends Controller
{
    public function getHomeScreen(){
        $offers = Offer::get();
        $events = Event::get();
        $question = Question::inRandomOrder()->first();
        return customResponse([
            "offers" => OffersResource::collection($offers),
            "events" => EventsResource::collection($events),
            "question_of_the_day"   => new QuestionResource($question),
            "specialities"          => [
                // @todo to be fetched from DB after karim finishes
            ],
            "courses"   => [
                // @todo to be fetched from db after i create courses table
            ]
        ], __("Get home content successfully"), true, 200);
    }
}
