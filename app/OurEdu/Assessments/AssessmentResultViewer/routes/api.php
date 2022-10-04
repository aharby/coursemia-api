<?php

use App\OurEdu\Assessments\AssessmentResultViewer\Controllers\Api\AssessmentReportsController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'result-viewers',
        'as' => 'result-viewers.',
        'namespace' => "\App\OurEdu\Assessments\AssessmentResultViewer\Controllers\Api"
    ],
    function () {

        Route::get('assessment-report', 'AssessmentReportsController@getAssessmentReport')->name('assessment-report');
        Route::get('assessment-report/export', 'AssessmentReportsController@exportAssessmentReport')->name('export-assessment-report');

        Route::get('assessors-report/{assessment}', 'AssessmentReportsController@getAssessmentAssessorReport')->name('assessors-report');
        Route::get('assessors-report/{assessment}/export', 'AssessmentReportsController@exportAssessmentAssessorsReport')->name('export-assessors-report');

        Route::get('assessor-assessee-report/{assessment}/{assessorId}', 'AssessmentReportsController@getAssessorAssesseesReport')->name('assessor-assessee-report');
        Route::get('assessor-assessee-report/{assessment}/{assessorId}/export', 'AssessmentReportsController@exportAssessorAssesseesReport')->name('export-assessor-assessee-report');

        Route::get('assessor-assessee-report-details/{assessment}/{assessor}/{assessee}', 'AssessmentReportsController@getAssessorAssesseesAssessmentsReport')->name('assessor-assessee-details-report');
        Route::get('assessor-assessee-report-details/{assessment}/{assessor}/{assessee}/export', 'AssessmentReportsController@exportAssessorAssesseesAssessmentsReport')->name('export-assessor-assessee--details-report');

        Route::get('assessor-assessee-answer/{assessmentUser}', 'AssessmentReportsController@getAssessorAnswer')->name('assessor-assessee-answer');
    }
);
