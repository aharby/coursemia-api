<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'assessments',
    'as' => 'result-viewers.',
    'namespace' => "\App\OurEdu\Assessments\AssessmentResultViewer\Controllers\Web"
], function () {
    Route::get("/reports", "AssessmentReportsController@index")->name("assessments.index");
    Route::get("/reports/exports", "AssessmentReportsController@indexExport")->name("assessments.index.exports");
    Route::get("/{assessment}/reports/assessors", "AssessmentReportsController@viewAssessmentAssessors")->name("assessments.assessors.view");
    Route::get("/{assessment}/reports/assessors/exports", "AssessmentReportsController@viewAssessmentAssessorsExport")->name("assessments.assessors.view.exports");
    Route::get("/{assessment}/reports/{assessor}/assessees", "AssessmentReportsController@viewAssessmentAssessees")->name("assessments.assessees.view");
    Route::get("/{assessment}/reports/{assessor}/assessees/exports", "AssessmentReportsController@viewAssessmentAssesseesExport")->name("assessments.assessees.view.exports");
    Route::get("/{assessment}/reports/{assessor}/assessees/{assessee}/assessments", "AssessmentReportsController@viewAssesseeAssessments")->name("assessments.assessees.view.details");
    Route::get("/{assessment}/reports/{assessor}/assessees/{assessee}/assessments/exports", "AssessmentReportsController@viewAssesseeAssessmentsExport")->name("assessments.assessees.view.details.export");
});
