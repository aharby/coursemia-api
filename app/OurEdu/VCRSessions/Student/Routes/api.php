<?php
    Route::group(['prefix'=>'online-sessions','as'=>'online-sessions.'], function () {
        Route::post('join-session/{sessionId}', '\App\OurEdu\VCRSessions\Student\Controllers\API\VCRServiceLiveSessionsController@studentJoinSession')->name('join-session');
    });
