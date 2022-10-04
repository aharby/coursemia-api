<?php
Route::group(
    ['prefix' => 'video-call','middleware'=>'auth:api'],
    function () {
        Route::post('/request', '\App\OurEdu\VideoCall\Controllers\Api\VideoCallController@videoCallRequest');
        Route::post('/request/cancel', '\App\OurEdu\VideoCall\Controllers\Api\VideoCallController@cancelVideoCall');
        Route::post('/status/update', '\App\OurEdu\VideoCall\Controllers\Api\VideoCallController@updateVideoCallStatus');
    }
);
Route::put('/video-call/{user}/leave', '\App\OurEdu\VideoCall\Controllers\Api\VideoCallController@LeaveVideoCall');

