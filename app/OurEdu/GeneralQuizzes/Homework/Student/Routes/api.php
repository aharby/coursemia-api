<?php
Route::group([
    'prefix' => 'student', 'as' => 'student.',

    'namespace'=>'\App\OurEdu\GeneralQuizzes\Homework\Student\Controllers\Api'
], function () {

    Route::get('list-homeworks/', 'HomeworkController@listHomeworks')
        ->name('get.list-homeworks');

    Route::post('start-homework/{homeworkId}', 'HomeworkController@startHomework')
        ->name('post.startHomework');

    Route::post('post-answer/{homeworkId}', 'HomeworkController@postAnswer')
        ->name('post.answer');

    Route::post('finish-homework/{homeworkId}', 'HomeworkController@finishHomework')
        ->name('post.finish');


    Route::get('feedback/{homework}', 'HomeworkController@feedback')
        ->name('post.feedback');

    Route::get('{homeworkId}/questions', 'HomeworkController@getNextOrBackQuestion')
        ->name('get.next-back-questions');

    Route::get('which-solved/{homework}', 'HomeworkController@whichStudentAnswered')
        ->name('get.which.solved');

});
