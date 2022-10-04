<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'students',
    'as' => 'students.',
    'namespace' => '\App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Controllers\Api'
], function () {
    Route::get("list", "PeriodicTestController@list")->name("list-periodic-test");

    Route::post('start/{periodicTest}', 'PeriodicTestController@startPeriodicTest')->name('post.start.periodic.test');

    Route::post('post-answer/{periodicTest}', 'PeriodicTestController@postAnswer')
        ->name('post.answer');

    Route::post('finish-periodic-test/{periodicTest}', 'PeriodicTestController@finishPeriodicTest')
        ->name('post.finish');

    Route::get('feedback/{periodicTest}', 'PeriodicTestController@feedback')
        ->name('post.feedback');

    Route::get('{periodicTest}/questions', 'PeriodicTestController@getNextOrBackQuestion')
        ->name('get.next-back-questions');

    Route::get('which-solved/{periodicTest}', 'PeriodicTestController@whichStudentAnswered')
        ->name('get.which.solved');

    Route::post('{periodicTest}/update-time', 'PeriodicTestController@updateStudentPeriodicTestTime')
        ->name('updateStudentPeriodicTestTime');

    Route::get('{periodicTest}/time-left', 'PeriodicTestController@studentPeriodicTestTimeLeft')
        ->name('studentPeriodicTestTimeLeft');
});

