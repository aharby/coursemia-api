<?php
    Route::group(['prefix'=>'online-sessions','as'=>'online-sessions.',
                    'namespace' => '\App\OurEdu\VCRSessions\Instructor\Controllers\API'], function () {
        Route::get('/', 'VCRServiceLiveSessionsController@listSessions')->name('listSessions');

        Route::get('/{sessionId}', 'VCRServiceLiveSessionsController@viewSession')
            ->name('viewSession');

        Route::get('/{sessionId}/participants', 'VCRServiceLiveSessionsController@listSessionParticipants')
            ->name('listSessionParticipants');

        Route::post('/{vCRSession}/show-records', 'VCRServiceLiveSessionsController@toggleShowRecords')
        ->name('toggleShowRecords');
    });
