<?php

Route::group(['prefix'=>'courses','as'=>'courses.'], function () {
    Route::get('/', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@listCourses')->name('listCourses');
    Route::get('/unsubscribed-top-qudrat', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@listUnsubscribedCourses')->name('qudrat.listCourses');
    Route::get('/top-qudrat', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@listsubScribedAndUnsubscribedCourses');
    Route::get('/subscribed', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@listSubscribedCourses');

    Route::get('list-sessions', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@listSessionCourses')->name('listSessionCourses');

    Route::get('/list/{student}', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@listCoursesForStudent')->name('listCoursesForStudent');

    Route::get('/instructor/{id}', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@instructorProfile')->name('instructorProfile');

    Route::get("/list-media/{course?}", '\App\OurEdu\Courses\Student\Controllers\CoursesMediaApiController@listCourseMedia');

    Route::get('/{id}', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@show')->name('show');

    Route::get('session/{id}', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@courseSession')->name('courseSession');

    //
    Route::post('/subscribe/{courseId}', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@subscribe')->name('subscribe');

    Route::post('/rate/{courseId}', '\App\OurEdu\Courses\Student\Controllers\CourseApiController@rateCourse')->name('rateCourse');
});


Route::group(['prefix'=>'live-sessions','as'=>'liveSessions.'], function () {

    // Student list live sessions
    Route::get('/','\App\OurEdu\Courses\Student\Controllers\LiveSessionApiController@listAvailable')->name('list');
    // temporary
    // The card is not ready yet
    Route::get('/{id}', '\App\OurEdu\Courses\Student\Controllers\LiveSessionApiController@show')->name('show');

    // temp
    Route::post('/subscribe/{liveSessionId}', '\App\OurEdu\Courses\Student\Controllers\LiveSessionApiController@subscribe')->name('subscribe');

    Route::post('/subscribe-and-join/{liveSessionId}', '\App\OurEdu\Courses\Student\Controllers\LiveSessionApiController@subscribeAndJoin')
        ->name('subscribeAndJoin');


});
