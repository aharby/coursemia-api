<?php

Route::group(['prefix'=>'classroom-class','as'=>'classroomClasses.',
    'namespace' => '\App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Controllers\Api'
], function () {

    Route::get('/', 'ClassroomClassController@getIndex')->name('index');

    Route::get('todays-sessions', 'ClassroomClassController@getTodaysClassRoomClasses')->name('get-todays-sessions');
});
