<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "school/supervisor",
    "namespace" => "\App\OurEdu\Quizzes\Controllers",
], function () {
    Route::get("quizzes", "QuizController@listAllQuizzes")->name("quiz.index");
    Route::get("quizzes/students/{quiz}", "QuizController@quizStudents")->name("quiz.students");
});
