<?php

Route::group([
    'prefix'=>'sessions',
    'as'=>'sessions.class.',
    'namespace' => '\App\OurEdu\SchoolAccounts\ClassroomClassSessions\SchoolSupervisor\Controllers'
    ], function () {
    Route::get('{classroomClass}', 'ClassroomClassSessionController@getIndex')->name('get.index');
    Route::get('{classroomClassSession}/edit', 'ClassroomClassSessionController@getEdit')->name('get.edit');
    Route::post('{classroomClassSession}/edit', 'ClassroomClassSessionController@postEdit')->name('post.edit');
    Route::get('{classroomClassSession}/delete', 'ClassroomClassSessionController@delete')->name('get.delete');
});
