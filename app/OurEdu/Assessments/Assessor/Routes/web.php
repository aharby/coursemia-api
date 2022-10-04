<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'assessments',
    'as' => 'assessor.',
    'namespace' => "\App\OurEdu\Assessments\Assessor\Controllers\Web"
], function () {
    Route::get("/", "AssessmentController@index")->name("assessments.index");
    Route::get("/{assessment}/assessees", "AssessmentController@listAssessees")->name("assessments.assessees.list");
});
