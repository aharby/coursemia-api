<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'instructor', 'as' => 'instructor.',
        'namespace' => '\App\OurEdu\GeneralQuizzes\Homework\Instructor\Controllers\Api'
    ],
    function () {
        Route::get('/list', 'HomeworkController@index')
            ->name('get.list');

        Route::get('/list/export', 'HomeworkController@ExportIndexData')
            ->name('get.export');

        Route::get('/view/{homework}', 'HomeworkController@show')
            ->name('get.view');

        Route::post('/create', 'HomeworkController@createHomeWork')
            ->name('post.create');

        Route::put('/edit/{homeworkId}', 'HomeworkController@editHomeWork')
            ->name('put.edit');

        Route::get('/{homework}/sections', 'HomeworkController@getHomeworkSection')
            ->name('get.sections');

        Route::post("{homework}/questions", "HomeworkQuestionController@store")->name('post.questions_store');

        Route::get("{homework}/questions/list", "HomeworkQuestionController@list")->name('get.questions_list');

        Route::get("{homework}/questions/{questionBank}/view", "HomeworkQuestionController@view")->name('view.question');

        Route::post('/publish/{homework}', 'HomeworkController@publish')
            ->name('post.publish');

        Route::delete('/delete/{homework}', 'HomeworkController@delete')
            ->name('delete.delete');

        Route::get('/{homework}/list-students-scores', 'HomeworkController@listStudentsScores')
            ->name('get.listStudentsScores');

        Route::get('/{homework}/export-students-scores', 'HomeworkController@exportStudentsScores')
            ->name('get.exportStudentsScores');

        Route::get('/{homework}/get-answers/{student}', 'HomeworkController@getStudentHomeworkAnswers')
            ->name('get.getStudentHomeworkAnswers');

        Route::get('/{homework}/get-answers-paginate/{student}', 'HomeworkController@getStudentAnswersSolved')
            ->name('get.getStudentAnswersSolved');

        Route::put('/{homework}/review-essay/{answer}', 'HomeworkQuestionController@reviewEssay')
            ->name('put.reviewEssay');

        Route::post('/add/question-bank/{homework}', 'HomeworkQuestionController@addQuestionBankToGeneralQuiz');

        Route::get('/question-bank/list/{homework}', 'HomeworkQuestionController@questionBankList');

        Route::get('/preview/{homework}', 'HomeworkController@preview')
            ->name('get.preview');

        Route::delete('{homework}/question/delete/{question}', 'HomeworkQuestionController@delete')
        ->name('delete.question');

        Route::post("retake/{homework}",  "HomeworkController@retake")->name("retake");

        Route::get("export/students-grades/{homework}", "HomeworkController@exportStudentsGrades")->name("grades.export");
    }
);
