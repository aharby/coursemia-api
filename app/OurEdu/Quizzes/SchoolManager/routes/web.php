<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "school/manager",
    "namespace" => "\App\OurEdu\Quizzes\SchoolManager\Controllers",
    "as" => "school.manager."
], function () {
    Route::get("quizzes", "QuizController@index")->name("quizzes.index");
    Route::get("quizzes/students/{quiz}", "QuizController@students")->name("quiz.students");
});
