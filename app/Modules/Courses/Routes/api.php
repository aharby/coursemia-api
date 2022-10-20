<?php

use App\Modules\Courses\Controllers\API\CoursesQuestionsAPIController;
use \App\Modules\Courses\Controllers\API\CoursesFlashCardsAPIController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['userActive'],
    'namespace' => '\App\Modules\Courses\Controllers\API',
    'prefix' => 'courses', 'as' => 'courses.'
], function (){
    Route::group(['middleware' => 'auth:api'], function (){
        Route::get('my-courses', 'CoursesAPIController@myCourses');

        Route::post('add-course-review', 'CourseReviewsAPIController@addCourseReview');
        Route::post('add-course-review', 'CourseReviewsAPIController@addCourseReview');

        // Submit Exam APIs
        Route::post('submit-exam', 'ExamQuestionsAndAnswersAPIController@getCourseFlashCards');
        // Submit Flashcards APIs
        Route::post('submit-flashcard', 'ExamQuestionsAndAnswersAPIController@submitFlashCardAnswer');

    });
    Route::post('get-courses', 'CoursesAPIController@courses');
    Route::get('get-course-details', 'CoursesAPIController@getCourseById');
    Route::get('get-course-reviews', 'CourseReviewsAPIController@reviews');
    Route::get('get-course-lectures', 'CoursesAPIController@getCourseLectures');
    Route::get('get-course-notes', 'CoursesAPIController@getCourseNotes');
    // Questions Apis
    Route::group([
        'prefix' => 'questions', 'as' => 'questions.'
    ], function (){
        Route::get('/{course_id}', [CoursesQuestionsAPIController::class , 'getCourseQuestions']);

    });

    // Flashcards Apis
    Route::group([
        'prefix' => 'flashcards', 'as' => 'flashcards.'
    ], function (){
        Route::get('/{course_id}', [CoursesFlashCardsAPIController::class , 'getCourseFlashCards']);

    });

});
