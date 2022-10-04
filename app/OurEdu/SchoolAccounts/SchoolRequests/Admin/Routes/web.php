<?php

Route::group([
        'prefix'=>'school-requests',
        'as'=>'school-requests.',
        'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolRequests\Admin\Controllers',
    ], function () {

    Route::get('/school-requests', 'SchoolRequestController@listSchoolRequests')->name('get.index');
    Route::get('/school-requests/approve/{id}', 'SchoolRequestController@approveSchoolRequests')->name('approve');

});
