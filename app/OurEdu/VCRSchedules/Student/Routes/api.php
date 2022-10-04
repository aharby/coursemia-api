<?php
Route::group(['prefix'=>'vcr','as'=>'vcr.'], function () {

    Route::post('{vcr}/new-request/{day}/{exam?}', '\App\OurEdu\VCRSchedules\Student\Controllers\Api\VCRRequestsController@postRequestVcr')->name('new.post.request');
    Route::get('available', '\App\OurEdu\VCRSchedules\Student\Controllers\Api\VCRRequestsController@availableRequestsBySubject')->name('get.availableRequestsBySubject');

    Route::post('rate/{sessionId}', '\App\OurEdu\VCRSchedules\Student\Controllers\Api\VCRSessionController@rateVCRSession')
        ->middleware(['auth:api', 'throttle:40000,60'])
        ->name('rateVCRSession');

    Route::get('view-session/{sessionId}', '\App\OurEdu\VCRSchedules\Student\Controllers\Api\VCRSessionController@view')->name('viewVCRSession');



    // temporary for reviewing only
    Route::get('available-instructors', '\App\OurEdu\VCRSchedules\Temporary\VCRTemporaryController@listAvailableInstructors')->name('listAvailableInstructors');
    Route::post('available-instructors/{vcrSchedule}', '\App\OurEdu\VCRSchedules\Temporary\VCRTemporaryController@requestVCRSession')->name('requestVCRSession');

    Route::get('available-instructors/get-session/{sessionId}', '\App\OurEdu\VCRSchedules\Temporary\VCRTemporaryController@getVCRSession')->name('getVCRSession');
});
