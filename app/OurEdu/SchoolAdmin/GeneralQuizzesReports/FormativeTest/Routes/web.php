<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'formative-tests-reports',
    'as'=>'formative-tests-reports.',
    'namespace'=>'\App\OurEdu\SchoolAdmin\GeneralQuizzesReports\FormativeTest\Controllers',
], function () {
    Route::get('/', 'FormativeTestController@index')->name('index');
    Route::get("export", "FormativeTestController@export")->name("export");
    Route::get("school/branches/{schoolAccount?}", "FormativeTestController@getSchoolBranches")->name("getSchoolBranches");
    Route::get("students/{generalQuiz}", "FormativeTestController@students")->name("students");
    Route::get("export/students/{generalQuiz}", "FormativeTestController@exportStudents")->name("students.export");
    Route::get("export/students-grades/{generalQuiz}", "FormativeTestController@exportStudentsGrades")->name(
        "grades.export"
    );
});
