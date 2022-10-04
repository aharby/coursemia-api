<?php
Route::group(['prefix' => 'instructor-competitions', 'as' => 'instructorCompetitions.',
    'namespace' => '\App\OurEdu\Exams\Instructor\Controllers\Api'], function () {

    Route::post('generate-competition/{vcr_session}', 'InstructorCompetitionApiController@generateInstructorCompetition')
        ->name('generateInstructorCompetition');

    Route::get('start-competition/{competitionId}', 'InstructorCompetitionApiController@startInstructorCompetition')
        ->name('startInstructorCompetition');

    Route::get('finish-competition/{competitionId}', 'InstructorCompetitionApiController@finishInstructorCompetition')
        ->name('finishInstructorCompetition');
});

Route::group(['prefix' => 'course-competitions', 'as' => 'courseCompetitions.',
    'namespace' => '\App\OurEdu\Exams\Instructor\Controllers\Api'], function () {

    Route::post('generate-competition/{course}', 'CourseCompetitionController@generateCourseCompetition')
        ->name('generateCourseCompetition');

    Route::get('feedback/{exam}', 'CourseCompetitionController@feedback')
        ->name('feedback');
    Route::get('index', 'CourseCompetitionController@index')
        ->name('index');
    Route::get('all', 'CourseCompetitionController@all')
        ->name('all');
});

Route::get('Look_up', '\App\OurEdu\Exams\Instructor\Controllers\Api\LookUpController@index')
    ->name('LookUpController');
