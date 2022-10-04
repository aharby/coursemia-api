<?php


Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {

    Route::get('/',
        '\App\OurEdu\Notifications\Controllers\Api\NotificationApiController@getAllNotifications')
        ->middleware(['auth:api', 'throttle:40000,60'])
        ->name('index');

    Route::get('/mark-read/{id}','\App\OurEdu\Notifications\Controllers\Api\NotificationApiController@markNotificationAsRead')
        ->name('mark-notification-read');

    Route::post('/update-token','\App\OurEdu\Notifications\Controllers\Api\NotificationApiController@updateUserToken')->name('updateUserToken');

    Route::get('/unread-count','\App\OurEdu\Notifications\Controllers\Api\NotificationApiController@getUnreadNotificationsCount')
        ->name('unread-notification-count');

});
