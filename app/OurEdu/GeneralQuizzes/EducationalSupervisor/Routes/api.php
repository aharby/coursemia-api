<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'educational-supervisor-reports',
    'as' => 'educational-supervisor-reports.',
    'namespace' => "\App\OurEdu\GeneralQuizzes\EducationalSupervisor\Controllers",
], function () {
    Route::get('list', "GeneralQuizReportsController@index");
    Route::get('export', "GeneralQuizReportsController@ExportIndexData");
    Route::get('/{generalQuiz}/list-students-scores', 'GeneralQuizReportsController@listStudentsScores')
        ->name('get.listStudentsScores');

    Route::get("export/students-grades/{generalQuiz}", "GeneralQuizReportsController@exportStudentsGrades")->name("grades.export");

    Route::get('/{generalQuiz}/get-answers-paginate/{student}', 'GeneralQuizReportsController@getStudentAnswersPaginates')
        ->name('get.getStudentAnswersSolved');

    Route::get('/{generalQuiz}/get-answers/{student}', 'GeneralQuizReportsController@getStudentAnswers')
        ->name('get.getStudentAnswers');
    Route::get('/{generalQuiz}/export-students-scores', 'GeneralQuizReportsController@exportStudentsScores')
        ->name('get.exportStudentsScores');
});
