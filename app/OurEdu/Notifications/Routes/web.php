<?php


Route::group(['prefix' => 'notifications', 'as' => 'notifications.', 'namespace' => '\App\OurEdu\Notifications\Controllers'], function () {

    Route::get('/', 'NotificationsController@getNotifications')->name('get.index');

});
