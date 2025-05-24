<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Courses\Controllers\API\CoursesQuestionsAPIController;
use \App\Modules\Courses\Controllers\API\CoursesFlashCardsAPIController;
use App\Modules\Courses\Controllers\API\CourseLectureAPIController;

Route::group([
    'middleware' => ['userActive'],
    'namespace' => '\App\Modules\Courses\Controllers\API',
    'prefix' => 'courses', 'as' => 'courses.'
], function (){
    Route::group(['middleware' => 'auth:api'], function (){
        Route::get('my-courses', 'CoursesAPIController@myCourses');

        Route::post('add-course-review', 'CourseReviewsAPIController@addCourseReview');
        Route::post('add-course-review', 'CourseReviewsAPIController@addCourseReview');

    });

     // Submit Exam APIs
    Route::post('submit-exam', 'ExamQuestionsAndAnswersAPIController@submitExamAnswers')->middleware('auth_or_guest');

    Route::post('get-courses', 'CoursesAPIController@courses');
    Route::get('get-course-details', 'CoursesAPIController@getCourseById');
    Route::get('get-course-reviews', 'CourseReviewsAPIController@reviews');
    Route::get('get-course-lectures', 'CoursesAPIController@getCourseLectures');
    Route::get('get-course-notes', 'CoursesAPIController@getCourseNotes');
    Route::post('submit-host-course-request', 'CoursesAPIController@submitHostCourseRequest');

    // Questions Apis
    Route::group([
        'prefix' => 'questions', 'as' => 'questions.'
    ], function (){
        Route::post('/', [CoursesQuestionsAPIController::class , 'getCourseQuestions']);

    });

    // Flashcards Apis
    Route::group([
        'prefix' => 'flashcards', 'as' => 'flashcards.'
    ], function (){
        Route::get('/', [CoursesFlashCardsAPIController::class , 'getCourseFlashCards']);

    });
    // Submit Flashcards APIs
    Route::post('submit-flashcard', 'ExamQuestionsAndAnswersAPIController@submitFlashCardAnswer');

});

    // lectures Apis
Route::group(['middleware' => 'auth:api',
    'prefix' => 'lectures', 'as' => 'lectures.'
], function (){
    //lecture save last position
    Route::post('/{lecture_id}/save-last-position', [CourseLectureAPIController::class, 'saveLastPosition']);
});
