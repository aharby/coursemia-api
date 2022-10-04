<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "general-quizzes",
    "namespace" => "\App\OurEdu\SchoolAdmin\GeneralQuizzes\Controllers",
    "as" => "general-quizzes."
], function () {
    Route::get("index/{branch?}", "GeneralQuizController@index")->name("index");
    Route::get("export/index/{branch?}", "GeneralQuizController@exportList")->name("index.export");
    Route::get("students/{generalQuiz}", "GeneralQuizController@students")->name("students");
    Route::get("export/students/{generalQuiz}", "GeneralQuizController@exportStudents")->name("students.export");
    Route::get("export/students-grades/{generalQuiz}", "GeneralQuizController@exportStudentsGrades")->name(
        "grades.export"
    );
    Route::get("questions/{generalQuiz}", "GeneralQuizzesQuestionController@index")->name("questions");
    Route::DELETE("questions/delete/{generalQuiz}/{generalQuizQuestionBank}", "GeneralQuizzesQuestionController@delete")->name("question.delete");

    Route::delete("delete/{generalQuiz}", "GeneralQuizController@delete")->name("delete");
});
