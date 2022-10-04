<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'reports', 'as' => 'reports.',
    'namespace' => '\App\OurEdu\Reports\Parent\Controllers'
], function () {

    Route::get('/student/absence', 'StudentReportController@absence')
        ->name('getStudentAbsence');
});
