<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'educational-supervisor', 'as' => 'educational-supervisor.',
        'namespace' => '\App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Controllers\Api'
    ],
    function () {

        Route::get('/list', 'PeriodicTestController@index')
            ->name('getList');

        Route::get('/view/{periodicTest}', 'PeriodicTestController@show')
            ->name('getView');

        Route::put('/edit/{periodicTest}', 'PeriodicTestController@editHomeWork')
            ->name('putEdit');

        Route::delete('/delete/{periodicTest}', 'PeriodicTestController@delete')
            ->name('deleteDelete');

        Route::get('/{periodicTest}/sections', 'PeriodicTestController@getPeriodicTestSection')
            ->name('getSections');

        Route::post('/deactivate/{periodicTest}', 'PeriodicTestController@deactivateHomework')
            ->name('postDeactivate');

        Route::get("{periodicTest}/questions/list", "PeriodicTestQuestionController@list")->name('getQuestionsList');

        Route::get("{periodicTest}/questions/{questionBank}/view", "PeriodicTestQuestionController@view")->name('viewQuestion');

        Route::post("{periodicTest}/questions", "PeriodicTestQuestionController@store")->name('postQuestionsStore');

        Route::delete('{periodicTest}/question/delete/{question}', 'PeriodicTestQuestionController@delete')
            ->name('deleteQuestion');

        Route::post('/add/question-bank/{periodicTest}', 'PeriodicTestQuestionController@addQuestionBankToGeneralQuiz');

        Route::get('/question-bank/list/{periodicTest}', 'PeriodicTestQuestionController@questionBankList');

        Route::get('/preview/{periodicTest}', 'PeriodicTestController@preview')
            ->name('get.periodicTest.preview');

    }
);
