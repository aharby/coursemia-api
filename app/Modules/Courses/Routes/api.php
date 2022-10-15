<?php

use App\Modules\Courses\Controllers\API\CoursesQuestionsAPIController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api','userActive'],
    'namespace' => '\App\Modules\Courses\Controllers\API',
    'prefix' => 'courses', 'as' => 'courses.'
], function (){
    Route::post('get-courses', 'CoursesAPIController@courses');
    Route::get('my-courses', 'CoursesAPIController@myCourses');
    Route::get('get-course-details', 'CoursesAPIController@getCourseById');
    Route::get('get-course-lectures', 'CoursesAPIController@getCourseLectures');
    Route::get('get-course-notes', 'CoursesAPIController@getCourseNotes');
    Route::get('get-course-reviews', 'CourseReviewsAPIController@reviews');
    Route::post('add-course-review', 'CourseReviewsAPIController@addCourseReview');
    Route::post('add-course-review', 'CourseReviewsAPIController@addCourseReview');



   // Questions Apis
    Route::group([
        'prefix' => 'questions', 'as' => 'questions.'
    ], function (){
        Route::get('/{course_id}', [CoursesQuestionsAPIController::class , 'getCourseQuestions']);

    });

});
