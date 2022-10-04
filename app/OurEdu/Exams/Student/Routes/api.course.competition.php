<?php

Route::group(
    ['prefix' => 'course-competitions', 'as' => 'courseCompetitions.'],
    function () {
        Route::get('join/{exam}', '\App\OurEdu\Exams\Student\Controllers\Api\CourseCompetitionApiController@joinCompetition')
            ->name('get.joinCompetition');
    }
);
