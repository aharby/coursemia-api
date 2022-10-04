<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "general-quizzes",
    "namespace" => "\App\OurEdu\GeneralQuizzes\SchoolManager\Controllers",
    "as" => "general-quizzes."
], function () {
    Route::get("index/{branch?}", "GeneralQuizController@index")->name("index");
    Route::get("trashed/classrooms", "GeneralQuizController@getTrashedClassrooms")->name("classrooms.trashed");
    Route::get("trashed/index/{classroomId}", "GeneralQuizController@indexTrashed")->name("index.trashed");
    Route::get("trashed/export/{classroomId}", "GeneralQuizController@exportTrashed")->name("trashed.exports");
    Route::get("export/index/{branch?}", "GeneralQuizController@exportList")->name("index.export");
    Route::get("students/{generalQuiz}", "GeneralQuizController@students")->name("students");
    Route::get("export/students/{generalQuiz}", "GeneralQuizController@exportStudents")->name("students.export");
    Route::get("export/students-grades/{generalQuiz}", "GeneralQuizController@exportStudentsGrades")->name("grades.export");
    Route::delete("delete/{generalQuiz}", "GeneralQuizController@delete")->name("delete");
});
