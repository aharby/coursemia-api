<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'total-percentages-reports',
    'as'=>'total-percentages-reports.',
    'namespace'=>'\App\OurEdu\SchoolAdmin\GeneralQuizzesReports\TotalPercentage\Controllers',
], function () {
    Route::get('/', 'TotalPercentageReportsController@getIndex')->name('get.index');
    Route::get("total-percentages-report-charts", "TotalPercentageReportsController@totalPercentagesChartReport")->name("report.charts");
    Route::get("total-percentages-report-export", "TotalPercentageReportsController@totalPercentagesReportExport")->name("report.export");
});
