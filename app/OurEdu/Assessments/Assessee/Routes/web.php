<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'assessee',
    'as' => 'assessee.',
    'namespace' => "\App\OurEdu\Assessments\Assessee\Controllers\Web"
], function () {
    Route::get("/assessments", "AssessmentController@listAssessments")->name("assessments.list");
    Route::get("/{assessment}/assessors", "AssessmentController@listAssessors")->name("assessors.list");
    Route::get("answers_attempts/{assessment}/{assessee}/{assessor}", "AssessmentController@getAssessorsAnswersAttempts")->name("answers.list");
});
