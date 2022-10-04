<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "general-quizzes-reports",
    "namespace" => "\App\OurEdu\GeneralQuizzesReports\SchoolSupervisor\Controllers",
    "as" => "general-quizzes-reports."
], function () {
    Route::get("total-percentages-report", "ReportsController@totalPercentagesReport")->name("total.percentages.report");
    Route::get("branch-level-report", "ReportsController@GeneralQuizBranchLevelReport")->name("branch-level-report");
    Route::get("student-reports-branch-levels-export", "ReportsController@generalQuizBranchLevelExport")->name("student.reports.branch.levels.export");
    Route::get("branch-level-report/students/{generalQuiz}", "ReportsController@generalQuizBranchLevelStudents")->name("branch-level-report.students");
    Route::get("branch-reports-class-levels", "ReportsController@classLevel")->name("branch.reports.class.levels");
    Route::get("student-reports-class-levels-export", "ReportsController@classLevelExport")->name("student.reports.class.levels.export");
    Route::get("branch-reports-skill-levels", "ReportsController@skillPercentageLevelReport")->name("branch.reports.skill.levels");
    Route::get("branch-reports-skill-levels-export", "ReportsController@skillPercentageLevelReportExport")->name("branch.reports.skill.levels.export");
    Route::get("branch-reports-skill-levels/{generalQuiz}",'ReportsController@sectionPercentageReport')->name("sectionPercentageReport");
    Route::get("branch-reports-skill-levels-export/{generalQuiz}",'ReportsController@sectionPercentageReportExport')->name("sectionPercentageReport.export");
    Route::get("branch-reports-instructor-levels", "ReportsController@instructorLevel")->name("branch.reports.instructor.levels");
    Route::get("branch-reports-instructor-levels-export", "ReportsController@instructorLevelExport")->name("branch.reports.instructor.levels.export");
    Route::get("branch-reports-subject-levels", "ReportsController@subjectLevel")->name("branch.reports.subject.levels");
    Route::get("branch-reports-subject-levels-export", "ReportsController@subjectLevelExport")->name("branch.reports.subject.levels.export");
    Route::get("question-percentage-report/index", "QuestionsPercentageReportController@index")->name("question.percentage.report.index");
    Route::get("question-percentage-report/questions/{generalQuiz}", "QuestionsPercentageReportController@questions")->name("question.percentage.report.questions");

    Route::get("students/", "StudentLevelReportsController@students")->name("student.level.students");
    Route::get("student-reports-student-levels-export", "StudentLevelReportsController@studentsExport")->name("student.reports.student.levels.export");
    Route::get("students/quizzes/{student}", "StudentLevelReportsController@studentQuizzes")->name("student.level.student.quizzes");
    Route::get("students/quizzes/{student}/export", "StudentLevelReportsController@studentQuizzesExport")->name("student.level.student.quizzes.export");
    Route::get("students/sections/{generalQuizStudent}", "StudentLevelReportsController@studentSectionPerformance")->name("student.level.student.section.performance");
    Route::get("students/sections/{generalQuizStudent}/export", "StudentLevelReportsController@studentSectionPerformanceExport")->name("student.level.student.section.performance.export");


    Route::get("total-percentages-report-charts", "ReportsChartsController@totalPercentagesReport")->name("total.percentages.report.charts");
    Route::get("branch-reports-class-levels-charts", "ReportsChartsController@classLevel")->name("branch.reports.class.levels.charts");
    Route::get("branch-reports-instructor-levels-charts", "ReportsChartsController@instructorLevel")->name("branch.reports.instructor.levels.charts");
    Route::get("branch-reports-skill-levels/{generalQuiz}/charts",'ReportsChartsController@sectionPercentageReport')->name("sectionPercentageReport.charts");
    Route::get("branch-reports-subject-levels_charts-charts", "ReportsChartsController@subjectLevel")->name("branch.reports.subject.levels.charts");

    Route::get("question-percentage-report/index-charts", "QuestionsPercentageReportChartsController@index")->name("question.percentage.report.index.charts");
    Route::get("question-percentage-report/questions/{generalQuiz}/charts", "QuestionsPercentageReportChartsController@questions")->name("question.percentage.report.questions.charts");

    Route::get("students/quizzes/{student}/charts", "StudentLevelReportsChartsController@studentQuizzes")->name("student.level.student.quizzes.charts");
    Route::get("students/sections/{generalQuizStudent}/charts", "StudentLevelReportsChartsController@studentSectionPerformance")->name("student.level.student.section.performance.charts");
    Route::get("question-percentage-report/questions/{generalQuiz}/export", "QuestionsPercentageReportController@exportQuestions")->name("question.percentage.report.details.export");
    Route::get('question-percentage-report/export', 'QuestionsPercentageReportController@export')->name('question.percentage.report.export');
});
