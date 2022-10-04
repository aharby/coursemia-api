<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'course-sessions','as'=>'courseSessions.'], function () {

    // Student list live sessions
    Route::get('/','\App\OurEdu\Courses\Instructor\Controllers\CourseSessionApiController@listAvailable')->name('list');
    Route::get('/{course}','\App\OurEdu\Courses\Instructor\Controllers\CourseSessionApiController@courseSessions')->name('list.course.sessions');
    // temporary


});

    Route::group(
        [
            "prefix"=>"courses",
            "as" => "courses.",
            "namespace"=>"\App\OurEdu\Courses\Instructor\Controllers"
        ],
        function () {
            Route::get("/", "CoursesApiController@index")->name("courses");
            Route::get("/students/{course}", "CoursesApiController@getStudents")->name("getStudents");

            Route::group(
                [
                    'middleware' => ['course_instructor']
                ],
                function () {
                    Route::get("/list-media/{course?}", "CoursesMediaApiController@listCourseMedia");
                    Route::post("/attache-media/{course}", "CoursesMediaApiController@attacheMediaToCourse")->name('attach-media');
                    Route::post("/detach-media/{course}", "CoursesMediaApiController@detachMediaFromCourse")->name('detach-media');
                }
            );
            Route::get("/change-media-status/{media}", "CoursesMediaApiController@toggleMediaStatus")->name('change-media-status');
        }
    );
