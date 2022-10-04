<?php
    Route::group(
        ['prefix'=>'online-sessions','as'=>'online-sessions.',
        'namespace'=>'\App\OurEdu\VCRSessions\General\Controllers\API',
        'middleware' => ['auth:api', 'throttle:40000,60']],
        function () {
            Route::get('get-session/{sessionId}', 'VCRGeneralLiveSessionsController@getVCRSession')->name('getVCRSession');
            Route::get('leave-session/{sessionId}', 'VCRGeneralLiveSessionsController@leaveVCRSession')->name('leaveVCRSession');

            Route::post('finish-session/{sessionId}/{type}', 'VCRGeneralLiveSessionsController@finishVCRSession')->name('finishVCRSession');
            Route::post('start-session/{sessionId}/{type}', 'VCRGeneralLiveSessionsController@startVCRSession')->name('startVCRSession');
            Route::get('get-image-session/{sessionId}/{type}', 'VCRGeneralLiveSessionsController@getImage')->name('getImage');
            Route::get('check-vcr-finished/{sessionId}', 'VCRGeneralLiveSessionsController@checkVcrFinished')->name('checkVcrFinished');

        // session files routes
            Route::post('{sessionId}/upload-file', 'VCRGeneralLiveSessionsController@uploadVCRFile')->name('uploadFile');
            Route::post('{sessionId}/upload-record-file', 'VCRGeneralLiveSessionsController@uploadRecordFile')->name('uploadRecordFile');
            Route::get('{sessionId}/recorded-files', 'VCRGeneralLiveSessionsController@getRecordedVcrFiles')->name('getRecordedFiles');
            Route::get('/{sessionId}/files', 'VCRGeneralLiveSessionsController@getVCRFiles')->name('getSessionFiles');

            Route::post('vcr-support', 'VCRGeneralLiveSessionsController@vcrSupport')->name('vcrSupport');
            Route::post('zoom-error-logs', 'VCRGeneralLiveSessionsController@zoomLogErrors')->name('zoom.log.errors');
            Route::post('test-test', 'VCRGeneralLiveSessionsController@testVcrNotification')->name('testVcrNotification');
        }
    );
