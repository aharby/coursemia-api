<?php
Route::group(['prefix'=>'subscriptions','as'=>'subscriptions.'], function () {
    Route::post('course/{id}/{studentId}', '\App\OurEdu\Subscribes\Parent\Controllers\SubscriptionsApiController@courseSubscripe')->name('post.courseSubscripe');


    Route::post('subject-package/{id}/{studentId}', '\App\OurEdu\Subscribes\Parent\Controllers\SubscriptionsApiController@subjectPackageSubscribe')->name('post.subjectPackageSubscribe');

    Route::post('subject/{id}/{studentId}', '\App\OurEdu\Subscribes\Parent\Controllers\SubscriptionsApiController@subjectSubscripe')->name('post.subjectSubscripe');
    // parent can't subscribe to liveSession
//    Route::post('live-session/{id}', '\App\OurEdu\Subscribes\Parent\Controllers\SubscriptionsApiController@liveSessionSubscripe')->name('post.liveSessionSubscripe');

    Route::get('user/{studentId}', '\App\OurEdu\Subscribes\Parent\Controllers\SubscriptionsApiController@userSubscriptions')->name('userSubscriptions');

    Route::get('payment/{id}', '\App\OurEdu\Subscribes\Parent\Controllers\SubscriptionsApiController@subscriptionPayment')->name('subscriptionPayment');
});
