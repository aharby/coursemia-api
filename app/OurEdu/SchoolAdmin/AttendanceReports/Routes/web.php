<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'attendance-reports',
    'as' => 'attendance-reports.',
    'namespace' => '\App\OurEdu\SchoolAdmin\AttendanceReports\Controllers',
], function () {
    Route::get("/user-attends", "StudentAttendanceController@getUserAttends")->name("user-attends");
    Route::get("/user-attends/export", "StudentAttendanceController@exportUserAttends")->name("user-attends.export");
    Route::get("/user-attends/sessions/export/{user}", "StudentAttendanceController@exportUserPresenceSessions")->name("user-attends.sessions.export");
});
