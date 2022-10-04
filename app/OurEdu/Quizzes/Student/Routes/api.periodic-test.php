<?php

Route::group(['prefix' => 'periodic-test', 'as' => 'periodic-test.',
    'namespace' => '\App\OurEdu\Quizzes\Student\Controllers\Api'
], function () {

    Route::get('/', 'PeriodicTestApiController@listPeriodicTest')
        ->name('get.list-student-periodicTest');

    Route::get('/{periodicTestId}', 'PeriodicTestApiController@getPeriodicTest')
        ->name('get.getPeriodicTest');

    Route::get('/start-periodic-test/{periodicTestId}/', 'PeriodicTestApiController@startPeriodicTest')
        ->name('get.start-periodic-test');

    Route::get('{periodicTestId}/questions', 'PeriodicTestApiController@getNextOrBackQuestion')
        ->name('get.next-back-questions');

    Route::get('/finish-periodic-test/{periodicTestId}/', 'PeriodicTestApiController@finishPeriodicTest')
        ->name('get.finish-quiz');

    Route::post('post-answer/{periodicTestId}', 'PeriodicTestApiController@postAnswer')
        ->name('post.answer');

});
