<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'assessments',
    'as' => 'assessments.',
    'namespace' => "\App\OurEdu\Assessments\SchoolAdmin\Controllers"
], function () {
    Route::get("/reports", "AssessmentReportsController@index")->name("index");
    Route::get("/reports/exports", "AssessmentReportsController@indexExport")->name("index.exports");
    Route::get("/{assessment}/reports/assessors", "AssessmentReportsController@viewAssessmentAssessors")->name("assessors.view");
    Route::get("/{assessment}/reports/assessors/exports", "AssessmentReportsController@viewAssessmentAssessorsExport")->name("assessors.view.exports");
    Route::get("/{assessment}/reports/{assessor}/assessees", "AssessmentReportsController@viewAssessmentAssessees")->name("assessees.view");
    Route::get("/{assessment}/reports/{assessor}/assessees/exports", "AssessmentReportsController@viewAssessmentAssesseesExport")->name("assessees.view.exports");
    Route::get("/{assessment}/reports/{assessor}/assessees/{assessee}/assessments", "AssessmentReportsController@viewAssesseeAssessments")->name("assessees.view.details");
    Route::get("/{assessment}/reports/{assessor}/assessees/{assessee}/assessments/exports", "AssessmentReportsController@viewAssesseeAssessmentsExport")->name("assessees.view.details.export");
});
