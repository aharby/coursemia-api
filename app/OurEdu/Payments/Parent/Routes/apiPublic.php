<?php
Route::group(['prefix' => 'payments', 'as' => 'payments.'], function () {

    Route::get('share-payments/{parentId}', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@sharePayments')
        ->name('export.payments');
    Route::get('share-expenses/{parentId}', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@shareExpenses')
        ->name('export.expenses');
});
