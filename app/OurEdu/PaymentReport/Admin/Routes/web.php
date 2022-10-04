<?php
Route::group([
    'prefix'=>'payment-report','as'=>'paymentReport.',
    'namespace'=>'\App\OurEdu\PaymentReport\Admin\Controllers'
], function () {

    Route::get('/', 'PaymentReportController@getIndex')->name('get.index');
    Route::get('/{id}/details', 'PaymentReportController@getDetails')->name('getDetails');

    Route::get('getProducts','PaymentReportController@getProducts')->name('getProducts');
    Route::get("/export", "PaymentReportController@indexExport")->name("index.exports");

});
