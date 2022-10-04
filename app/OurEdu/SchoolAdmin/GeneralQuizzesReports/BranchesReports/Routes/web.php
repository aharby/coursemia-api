<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'branch-reports',
    'as'=>'branch-reports.',
    'namespace'=>'\App\OurEdu\SchoolAdmin\GeneralQuizzesReports\BranchesReports\Controllers',
], function () {
    Route::get("instructor-levels", "InstructorLevelReportsControllers@instructorLevel")->name("instructor.levels");
    Route::get("instructor-levels-export", "InstructorLevelReportsControllers@instructorLevelExport")->name("instructor.levels.export");
    Route::get("branch-reports-instructor-levels-charts", "InstructorLevelReportsControllers@instructorLevelChart")->name("instructor.levels.charts");
    Route::get("subject-levels", "SubjectLevelReportsControllers@subjectLevel")->name("subject.levels");
    Route::get("branch-reports-subject-levels-export", "SubjectLevelReportsControllers@subjectLevelExport")->name("subject.levels.export");
    Route::get("branch-reports-subject-levels_charts-charts", "SubjectLevelReportsControllers@subjectLevelChart")->name("subject.levels.charts");
    Route::get("branch-reports-skill-levels", "SkillsLevelReportsControllers@skillPercentageLevelReport")->name("skill.levels");
    Route::get("branch-reports-skill-levels-export", "SkillsLevelReportsControllers@skillPercentageLevelReportExport")->name("skill.levels.export");
    Route::get("branch-reports-skill-levels/{generalQuiz}",'SkillsLevelReportsControllers@sectionPercentageReport')->name("sectionPercentageReport");
    Route::get("branch-reports-skill-levels-export/{generalQuiz}",'SkillsLevelReportsControllers@sectionPercentageReportExport')->name("sectionPercentageReport.export");
    Route::get("branch-reports-skill-levels/{generalQuiz}/charts",'SkillsLevelReportsControllers@sectionPercentageReportChart')->name("sectionPercentageReport.charts");
    Route::get("question-percentage-report/index", "QuestionsPercentageLevelReportsControllers@index")->name("question.percentage.report.index");
    Route::get("question-percentage-report/questions/{generalQuiz}", "QuestionsPercentageLevelReportsControllers@questions")->name("question.percentage.report.questions");
    Route::get("question-percentage-report/index-charts", "QuestionsPercentageLevelReportsControllers@indexChart")->name("question.percentage.report.index.charts");
    Route::get("question-percentage-report/questions/{generalQuiz}/charts", "QuestionsPercentageLevelReportsControllers@questionsChart")->name("question.percentage.report.questions.charts");
    Route::get("question-percentage-report/questions/{generalQuiz}/export", "QuestionsPercentageLevelReportsControllers@exportQuestions")->name("question.percentage.report.details.export");
    Route::get('question-percentage-report/export', 'QuestionsPercentageLevelReportsControllers@export')->name('question.percentage.report.export');

});
