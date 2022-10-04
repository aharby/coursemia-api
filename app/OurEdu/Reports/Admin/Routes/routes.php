<?php
Route::group(['prefix'=>'reports','as'=>'reports.'], function () {

    Route::get('/', '\App\OurEdu\Reports\Admin\Controllers\ReportControllers@getIndex')->name('get.index');
    Route::get('/details/{id}', '\App\OurEdu\Reports\Admin\Controllers\ReportControllers@getDetails');


});
