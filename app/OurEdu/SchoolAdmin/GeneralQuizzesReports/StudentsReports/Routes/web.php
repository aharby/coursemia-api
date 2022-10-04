<?php

use Illuminate\Support\Facades\Route;

    Route::group([
        "namespace" =>'\App\OurEdu\SchoolAdmin\GeneralQuizzesReports\StudentsReports\Controllers'
    ],function(){

        Route::get("branch-reports-class-levels", "ClassLevelReportController@classLevel")->name("branch.reports.class.levels");
        Route::get("student-reports-class-levels-export", "ClassLevelReportController@classLevelExport")->name("student.reports.class.levels.export");
        Route::get("branch-reports-class-levels-charts", "ClassLevelReportController@classLevelChart")->name("branch.reports.class.levels.charts");
    

        Route::get("branch-level-report", "BranchLevelReportController@GeneralQuizBranchLevelReport")->name("branch-level-report");
        Route::get("student-reports-branch-levels-export", "BranchLevelReportController@generalQuizBranchLevelExport")->name("student.reports.branch.levels.export");
        Route::get("branch-level-report/students/{generalQuiz}", "BranchLevelReportController@generalQuizBranchLevelStudents")->name("branch-level-report.students");
    

        Route::get("students", "StudentLevelReportController@students")->name("student.level.students");
        Route::get("student-reports-student-levels-export", "StudentLevelReportController@studentsExport")->name("student.reports.student.levels.export");
        Route::get("students/quizzes/{student}", "StudentLevelReportController@studentQuizzes")->name("student.level.student.quizzes");
        Route::get("students/quizzes/{student}/export", "StudentLevelReportController@studentQuizzesExport")->name("student.level.student.quizzes.export");
        Route::get("students/sections/{generalQuizStudent}", "StudentLevelReportController@studentSectionPerformance")->name("student.level.student.section.performance");
        Route::get("students/sections/{generalQuizStudent}/export", "StudentLevelReportController@studentSectionPerformanceExport")->name("student.level.student.section.performance.export");
    

        Route::get("students/quizzes/{student}/charts", "StudentLevelReportController@studentQuizzesChart")->name("student.level.student.quizzes.charts");
        Route::get("students/sections/{generalQuizStudent}/charts", "StudentLevelReportController@studentSectionPerformanceChart")->name("student.level.student.section.performance.charts");
    
    });
