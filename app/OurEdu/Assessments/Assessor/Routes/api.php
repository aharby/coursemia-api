<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'assessor',
        'as' => 'assessor.',
        'namespace' => "\App\OurEdu\Assessments\Assessor\Controllers\Api"
    ],
    function () {
        Route::get("/", "AssessmentController@index")->name("assessor.index");

        Route::get("/assessees/{assessment}", "AssessmentController@listAssessorAssesses")->name("get.assessees.index");

        Route::post('start-assessment/{assessmentId}/{assesseeId}', 'AssessmentController@startAssessment')
        ->name('post.startAssessment');
        Route::get('{assessment}/questions/{assesseeId}', 'AssessmentController@getNextOrBackQuestion')
            ->name('get.next-back-questions');

        Route::post('post-answer/{assessment}', 'AssessmentController@postAnswer')
        ->name('post.answer');

        Route::post('post-general-comment/{assessment}/{assesseeId}', 'AssessmentController@postGeneralComment')
        ->name('post.general.comment');


        Route::post('finish-assessment/{assessment}/{assesseeId}', 'AssessmentController@finishAssessment')
        ->name('post.finish');

    }
);
