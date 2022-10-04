<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'vcr', 'as' => 'vcr.'], function () {
    Route::get('requests', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRRequestsController@getVcrRequests')->name('getVcrRequests');

    Route::get('schedules', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRSchedulesController@getNextWeekVcrSchedule')->name('getVcrSchedules');

    Route::get('student-report/{requestId}', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRRequestsController@getStudentReport')->name('getStudentReport');

    Route::post('accept-request/{requestId}', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRRequestsController@acceptVcrRequest')->name('acceptVcrRequest');

    Route::get('presence-students/{VCRSession}', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRRequestsController@VCRPresenceStudents')->name('VCRPresenceStudents');

    Route::get('schedules/all', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRSchedulesController@getVCRSchedules')->name('getVcrSchedules.all');

    Route::get('schedules/sessions/{schedule}', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRSchedulesController@getVCRScheduleSessions')->name('getVcrSchedules.sessions');

    Route::get('info/{VCRSession}', '\App\OurEdu\VCRSchedules\Instructor\Controllers\Api\VCRSchedulesController@getRequestInfo')->name('get.info');

});
