<?php
Route::group(['prefix'=>'school-requests','as'=>'school-requests.'], function () {
    Route::post('/', '\App\OurEdu\SchoolAccounts\SchoolRequests\Controllers\SchoolRequestController@addRequest')->name('post.addRequest');
});
