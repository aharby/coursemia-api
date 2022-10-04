<?php

Route::group(['prefix'=>'exams','as'=>'exams.'], function () {
    Route::get('/student-grades/{subjectId}', '\App\OurEdu\Exams\Admin\Controllers\ExamController@getStudentGrades')->name('get.getStudentGrades');


    // AJAX routes
    Route::get('/country-systems', '\App\OurEdu\Exams\Admin\Controllers\AjaxExamController@countrySystems')->name('countrySystems');

    Route::get('/system-subjects', '\App\OurEdu\Exams\Admin\Controllers\AjaxExamController@systemSubjects')->name('systemSubjects');
});
