<?php

use App\OurEdu\Payments\Parent\Controllers\PaymentsApiController;

Route::group(['prefix' => 'payments', 'as' => 'payments.'], function () {
    Route::post(
        'transaction/feedback',
        '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@transactionFeedback'
    )
        ->name('transaction.feedback');

    Route::get('/', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@index')
        ->name('index');

    Route::get('expenses', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@expenses')
        ->name('expenses');
    Route::get('spending', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@spending')
        ->name('spending');

    Route::get('total-spending', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@totalSpending')
        ->name('total-spending');
    Route::get('export', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@exportPaymenent');
    Route::get('{transaction}/details', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@details')
        ->name('transaction.details');

    Route::get('expenses/export', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@expensesExport')
        ->name('expenses.export');

    Route::get('children/purchases', '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@childrenPurchases')
        ->name('children.purchases');

    Route::get(
        'transactions/count/{period}',
        '\App\OurEdu\Payments\Parent\Controllers\PaymentsApiController@getTransactionsByPeriod'
    )->name('getTransactionsByPeriod');

    Route::get(
        '/children/received-transactions',
        [PaymentsApiController::class, 'listChildrenReceivedTransactions']
    );
    Route::get(
        '/children/export-received-transactions',
        [PaymentsApiController::class, 'exportChildrenReceivedTransactions']
    );
    Route::post(
        'submit-transaction/{student_id}',
        [PaymentsApiController::class, 'submitTransaction']
    )->name('submitTransaction');

    Route::post('in-app/purchase/subscribe', '\App\OurEdu\Payments\Parent\Controllers\IAPController@subscribe')
        ->name('in-app.purchase');

    Route::post('in-app/purchase/verify', '\App\OurEdu\Payments\Parent\Controllers\IAPController@verify')
        ->name('in-app.purchase.verify');

    Route::post('in-app/purchase/cancel', '\App\OurEdu\Payments\Parent\Controllers\IAPController@cancelSubscription')
        ->name('in-app.purchase.cancel');
});
