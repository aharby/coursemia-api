<?php


use Illuminate\Support\Facades\Route;

Route::group([
    "prefix"=>"courses",
    "as" => "courses.",
    "namespace"=>"\App\OurEdu\Courses\Instructor\Controllers\V2"
    ], function () {
    Route::get("/{course}/sessions", "CoursesApiController@index")->name("course.sessions");
});
