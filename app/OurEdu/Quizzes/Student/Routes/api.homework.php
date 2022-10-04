<?php

Route::group(['prefix' => 'homework', 'as' => 'homework.',
    'namespace' => '\App\OurEdu\Quizzes\Student\Controllers\Api'
], function () {

    Route::get('/', 'HomeWorkApiController@listHomework')
        ->name('get.list-student-homework');

    Route::get('/{homeworkId}', 'HomeWorkApiController@getHomework')
        ->name('get.view-homework');

    Route::get('/start-homework/{homeworkId}/', 'HomeWorkApiController@startHomework')
        ->name('get.start-homework');

    Route::get('{homeworkId}/questions', 'HomeWorkApiController@getNextOrBackQuestion')
        ->name('get.next-back-questions');

    Route::get('/finish-homework/{homeworkId}/', 'HomeWorkApiController@finishHomework')
        ->name('get.finish-homework');

    Route::post('post-answer/{homeworkId}', 'HomeWorkApiController@postAnswer')
        ->name('post.answer');
});
