<?php
Route::group(['prefix'=>'learning-performance','as'=>'learningperformance.'], function () {

    Route::get('subject/{subjectId}', '\App\OurEdu\LearningPerformance\Student\Controllers\Api\LearningPerformanceController@getStudentOrderInSubject')->name('get.StudentOrderInSubject');

});
