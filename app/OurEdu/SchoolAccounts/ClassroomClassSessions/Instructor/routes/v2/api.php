<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'classroom-class','as'=>'classroomClasses.'], function () {

    Route::get('/', '\App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Controllers\Api\V2\ClassroomClassController@getIndex')->name('index.v2');
});
