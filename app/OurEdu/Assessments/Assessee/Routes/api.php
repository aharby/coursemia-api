<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'assessee',
        'as' => 'assessee.',
        'namespace' => "\App\OurEdu\Assessments\Assessee\Controllers\Api"
    ],
    function () {
        Route::get("/assessments", "AssessmentController@listAssessments")->name("assessments.list");
        Route::get("/{assessment}/assessors", "AssessmentController@listAssessors")->name("assessors.list");
    }
);
