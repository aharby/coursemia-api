<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => "\App\OurEdu\GeneralQuizzes\Student\Controllers",
        'prefix' =>"general-quizzes",
        'as' => "general-quizzes"
    ], function () {
        Route::get("show-result", "GeneralQuizReports@resultReport")->name("student.show.result.lookup");

        // Look Up Route
        Route::get("lookup", "LookUpController@lookUp")->name("student.show.result");
    }
);
