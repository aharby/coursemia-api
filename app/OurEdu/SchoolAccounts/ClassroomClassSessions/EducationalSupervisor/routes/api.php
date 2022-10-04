<?php

Route::group(['prefix'=>'classroom-class','as'=>'educational.supervisor.classroomClasses.'], function () {

    Route::get('', '\App\OurEdu\SchoolAccounts\ClassroomClassSessions\EducationalSupervisor\Controllers\Api\ClassroomClassController@index')->name('index');
});
