<?php
Route::group(
    ['prefix'=>'online-sessions','as'=>'online-sessions.',
        'middleware' => ['auth:api', 'throttle:40000,60'],
        'namespace' => '\App\OurEdu\VCRSessions\General\Controllers\API\V2'
    ],
    function () {
        Route::get('get-session/{vcrSession}', 'VCRGeneralLiveSessionsController@getVCRSession')->name('getVCRSessionV2');
        Route::get('get-session-zoom', 'VCRGeneralLiveSessionsController@getZoom')->name('getZoom');
    }
);
