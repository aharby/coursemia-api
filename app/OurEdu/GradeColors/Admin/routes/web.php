<?php

use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "grade/colors", "as" => "grade.colors.", "namespace" => "\App\OurEdu\GradeColors\Admin\Controllers"], function () {
    Route::get("/", "GradeColorController@index")->name("index");
    Route::get("/assign/grade/{gradeColor}", "GradeColorController@assignGrade")->name("assign.grades");
    Route::put("/assign/grade/{gradeColor}", "GradeColorController@postAssignGrade")->name("assign.grades.post");
});
