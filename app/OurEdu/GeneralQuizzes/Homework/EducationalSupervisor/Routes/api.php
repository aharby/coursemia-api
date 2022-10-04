<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'educational-supervisor', 'as' => 'educational-supervisor.',
        'namespace' => '\App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Controllers\Api'
    ],
    function () {

        Route::get('/list', 'HomeworkController@index')
            ->name('get.list');

        Route::get('/view/{homework}', 'HomeworkController@show')
            ->name('get.view');

        Route::put('/edit/{homework}', 'HomeworkController@editHomeWork')
            ->name('put.edit');

        Route::delete('/delete/{homework}', 'HomeworkController@delete')
            ->name('delete.delete');

        Route::post('/deactivate/{homework}', 'HomeworkController@deactivateHomework')
            ->name('post.deactivate');

        Route::get("{homework}/questions/list", "HomeworkQuestionController@list")->name('get.questions_list');

        Route::get("{homework}/questions/{questionBank}/view", "HomeworkQuestionController@view")->name('view.question');

        Route::post("{homework}/questions", "HomeworkQuestionController@store")->name('post.questions_store');

        Route::delete('{homework}/question/delete/{question}', 'HomeworkQuestionController@delete')
            ->name('delete.question');

        Route::get('/{homework}/sections', 'HomeworkController@getHomeworkSection')
            ->name('get.sections');
        Route::post('/add/question-bank/{homework}', 'HomeworkQuestionController@addQuestionBankToGeneralQuiz');

        Route::get('/question-bank/list/{homework}', 'HomeworkQuestionController@questionBankList');

        Route::get('/preview/{homework}', 'HomeworkController@preview')
            ->name('get.homework.preview');

    }
);
