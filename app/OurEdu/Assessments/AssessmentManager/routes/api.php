<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'assessment-manager',
        'as' => 'assessment-manager.',
        'namespace' => '\App\OurEdu\Assessments\AssessmentManager\Controllers\Api'
    ],
    function () {
        Route::post('/create', 'AssessmentController@createAssessment')
            ->name('post.create');

        Route::get('/list', 'AssessmentController@index')
            ->name('get.list');

        Route::get('/view/{assessment}', 'AssessmentController@show')
            ->name('get.view');

        Route::delete('/delete/{assessment}', 'AssessmentController@delete')
            ->name('delete.delete');

        Route::post('/publish/{assessment}', 'AssessmentController@publish')
            ->name('post.publish');

        Route::post('/unpublish/{assessment}', 'AssessmentController@unpublish')
            ->name('post.unpublish');

        Route::put('/edit/{assessmentId}', 'AssessmentController@edit')
            ->name('put.edit');

        Route::get("{assessment}/questions/list", "AssessmentQuestionController@list")
            ->name('get.questions_list');

        Route::post("{assessment}/questions", "AssessmentQuestionController@store")
            ->name('post.questions_store');

        Route::get("{assessment}/questions/{assessmentQuestion}/view", "AssessmentQuestionController@view")
            ->name('view.question');

        Route::delete('{assessment}/question/delete/{assessmentQuestion}', 'AssessmentQuestionController@delete')
            ->name('delete.question');

        Route::post('/{assessment}/rates', 'AssessmentController@storeRates')
            ->name('post.storeRates');

        Route::get('/{assessment}/rates', 'AssessmentController@getAssessmentPointsRates')
            ->name('get.rates.index');

        Route::get('assessment-report', 'AssessmentReportsController@getAssessmentReport')
            ->name('assessment-report');

        Route::get('assessment-report/export', 'AssessmentReportsController@exportAssessmentReport')
            ->name('export-assessment-report');

        Route::get(
            'assessors-report/{assessment}',
            'AssessmentReportsController@getAssessmentAssessorReport'
        )
            ->name('assessors-report');

        Route::get(
            'assessors-report/{assessment}/export',
            'AssessmentReportsController@exportAssessmentAssessorsReport'
        )
            ->name('export-assessors-report');

        Route::get(
            'assessor-assessee-report/{assessment}/{assessorId}',
            'AssessmentReportsController@getAssessorAssesseesReport'
        )
            ->name('assessor-assessee-report');

        Route::get(
            'assessor-assessee-report/{assessment}/{assessorId}/export',
            'AssessmentReportsController@exportAssessorAssesseesReport'
        )->name('export-assessor-assessee-report');

        Route::get(
            'assessor-assessee-report-details/{assessment}/{assessor}/{assessee}',
            'AssessmentReportsController@getAssessorAssesseesAssessmentsReport'
        )->name('assessor-assessee-details-report');

        Route::get(
            'assessor-assessee-report-details/{assessment}/{assessor}/{assessee}/export',
            'AssessmentReportsController@exportAssessorAssesseesAssessmentsReport'
        )->name('export-assessor-assessee--details-report');

        Route::get('at-question-report', 'AssessmentReportsController@atQuestionReport')
            ->name('at-question-report');

        Route::get('at-question-report-export', 'AssessmentReportsController@atQuestionReportExport')
            ->name('at-question-report-export');

        Route::get('questions-report/{assessment}', 'AssessmentReportsController@QuestionReport')
            ->name('questions-report');

        Route::get('questions-report-export/{assessment}', 'AssessmentReportsController@QuestionReportExport')
            ->name('questions-report-expor');

        Route::post('clone-assessment/{assessment}', 'AssessmentController@cloneAssessment')
            ->name('post.cloneAssessment');

        Route::get('/preview/{assessment}', 'AssessmentController@preview')
            ->name('get.preview');

        Route::get('average-question-report', 'AssessmentReportsController@assessmentWithAverageQuestions')
            ->name('questions.average');

        Route::get(
            'average-question-report/export',
            'AssessmentReportsController@assessmentWithAverageQuestionsExport'
        )->name('questions.average.export');

        Route::get('{assessment}/categories', 'AssessmentsCategoryController@index')
            ->name('categories.index');

        Route::post('categories/create', 'AssessmentsCategoryController@create')
            ->name('categories.create');

        Route::put('categories/update/{assessmentCategory}', 'AssessmentsCategoryController@edit')
            ->name('categories.update');

        Route::delete('categories/delete/{assessmentCategory}', 'AssessmentsCategoryController@delete')->name('categories.delete');
        Route::post("{assessment}/questions/{assessmentQuestion}/clone", "AssessmentQuestionController@postCloneQuestion")->name('post.cloneQuestion');
        Route::get(
            '{assessment}/answers-percentage',
            'AssessmentReportsController@assessmentAnswersPercentage'
        );
    }
);
