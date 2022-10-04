<?php
Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {

    Route::get('/', '\App\OurEdu\Reports\SME\Controllers\Api\ReportApiController@listReports')
        ->name('list.reports');

    Route::get('/subjects', '\App\OurEdu\Reports\SME\Controllers\Api\ReportApiController@listSubjects')
        ->name('list.reports.subjects');

    Route::get('/subjects/{subject}', '\App\OurEdu\Reports\SME\Controllers\Api\ReportApiController@listSubjectSections')
        ->name('list.reports.subject');

    Route::get('/sections/{section}', '\App\OurEdu\Reports\SME\Controllers\Api\ReportApiController@listSectionSections')
        ->name('list.reports.sections');
//
//    Route::get('/view-report/{reportId}', '\App\OurEdu\Reports\SME\Controllers\Api\ReportApiController@viewReport')
//        ->name('view.report');

});
