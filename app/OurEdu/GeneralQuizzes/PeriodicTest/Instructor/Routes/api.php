<?php

Route::group(
    [
        'prefix' => 'instructors', 'as' => 'instructors.',
        'namespace' => '\App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Controllers\Api'
    ],

    function () {
        Route::get('/list', 'PeriodicTestController@index')
            ->name('get.list');

        Route::get('/list/export', 'PeriodicTestController@ExportIndexData')
            ->name('get.export');

        Route::get('/view/{periodicTest}', 'PeriodicTestController@show')
            ->name('get.view');


        Route::post('/create', 'PeriodicTestController@createPeriodicTest')
            ->name('post.create');

        Route::put('/edit/{periodicTestId}', 'PeriodicTestController@editPeriodicTest')
            ->name('put.edit');


        Route::get('/{periodicTest}/sections', 'PeriodicTestController@getPeriodicTestSection')
            ->name('get.sections');


        Route::post("{periodicTest}/questions", "PeriodicTestQuestionController@store")->name('post.questions_store');


        Route::get("{periodicTest}/questions/list", "PeriodicTestQuestionController@list")->name('get.questions_list');


        Route::get("{periodicTest}/questions/{questionBank}/view", "PeriodicTestQuestionController@view")->name('view.question');


        Route::post('/publish/{periodicTest}', 'PeriodicTestController@publish')
            ->name('post.publish');


        Route::delete('/delete/{periodicTest}', 'PeriodicTestController@delete')
            ->name('delete.delete');

        Route::get('/{periodicTest}/list-students-scores', 'PeriodicTestController@listStudentsScores')
            ->name('get.listStudentsScores');

        Route::get('/{periodicTest}/export-students-scores', 'PeriodicTestController@exportStudentsScores')
            ->name('get.exportStudentsScores');

        Route::get('/{periodicTest}/get-answers/{student}', 'PeriodicTestController@getStudentPeriodicTestAnswers')
            ->name('get.getStudentPeriodicTestAnswers');
        Route::put('/{periodicTest}/review-essay/{answer}', 'PeriodicTestQuestionController@reviewEssay')
            ->name('put.reviewEssay');

        Route::post('/add/question-bank/{periodicTest}', 'PeriodicTestQuestionController@addQuestionBankToGeneralQuiz');

        Route::get('/question-bank/list/{periodicTest}', 'PeriodicTestQuestionController@questionBankList');

        Route::delete('{periodicTest}/question/delete/{question}', 'PeriodicTestQuestionController@delete')
                ->name('delete.question');

        Route::get('/preview/{periodicTest}', 'PeriodicTestController@preview')
                ->name('get.preview');

        Route::post("retake/{periodicTest}",  "PeriodicTestController@retake")->name("retake");

        Route::get('/{periodicTest}/get-answers-paginate/{student}', 'PeriodicTestController@getStudentAnswersSolved')
            ->name('get.getStudentAnswersSolved');

        Route::get("export/students-grades/{periodicTest}", "PeriodicTestController@exportStudentsGrades")->name("grades.export");

    }
);
