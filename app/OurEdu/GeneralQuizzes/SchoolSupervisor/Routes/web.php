<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "general-quizzes",
    "namespace" => "\App\OurEdu\GeneralQuizzes\SchoolSupervisor\Controllers",
    "as" => "general-quizzes."
], function () {
    Route::get("index/{branch?}", "GeneralQuizController@index")->name("index");
    Route::get("trashed/index/{classroomId}", "GeneralQuizController@indexTrashed")->name("index.trashed");
    Route::get("export/{branch?}", "GeneralQuizController@exportList")->name("index.exports");
    Route::get("trashed/export/{classroomId}", "GeneralQuizController@exportTrashed")->name("trashed.exports");
    Route::get("students/{generalQuiz}", "GeneralQuizController@students")->name("students");
    Route::get("trashed/students/{generalQuiz}", "GeneralQuizController@trashedClassroomStudents")->name("students.trashed");
    Route::get("export/students/{generalQuiz}", "GeneralQuizController@exportStudents")->name("students.export");
    Route::get("publish/{generalQuiz}", "GeneralQuizController@publish")->name("publish");
    Route::get("deactivate/{generalQuiz}", "GeneralQuizController@deactivate")->name("deactivate");
    Route::get("toggle/prevent/result-show/{student}", "GeneralQuizController@toggleShowingResultFlag")->name("prevent.result-show.toggle");
    Route::get("toggle/all/prevent/result-show/{generalQuiz}", "GeneralQuizController@toggleShowingResultFlagOnAll")->name("prevent.result-show.toggle.all");
    Route::get("questions/{generalQuiz}", "GeneralQuizQuestionController@list")->name("questions");
    Route::get("question/delete/{generalQuiz}/{question}", "GeneralQuizQuestionController@delete")->name("questions.delete");
    Route::get("export/students-grades/{generalQuiz}", "GeneralQuizController@exportStudentsGrades")->name("grades.export");
    Route::delete("delete/{generalQuiz}", "GeneralQuizController@delete")->name("delete");
});
