<?php

Route::group([
    'prefix' => 'payments',
    'as' => 'payments.',
    'namespace' => '\App\OurEdu\Payments\Admin\Controllers'
], function () {
    Route::get('/failed-transactions', 'PaymentController@getFailedTransactions')->name('failed_transactions');
    Route::get('/failed-transactions/export', 'PaymentController@exportFailedTransactions')->name('export_failed_transactions');
});
