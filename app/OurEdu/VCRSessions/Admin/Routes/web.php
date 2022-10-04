<?php
    Route::group(['prefix'=>'vcr-sessions','as'=>'vcr-sessions.'], function () {
        Route::get('/video', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRServiceSessionsAdminController@getIndex')->name('vcr-sessions.getIndex');
        Route::get('/whiteboard', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRServiceSessionsAdminController@getWhiteboard')->name('vcr-sessions.getWhiteboard');
        Route::get('/subjects', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@subjectVCR')->name('vcr-sessions.subjects');
        Route::get('/subjects/export', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@subjectVCRExport')->name('vcr-sessions.subjects.export');
        Route::get('/subjects/{subject}/vcr-schedule', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@subjectVCRSchedules')->name('vcr-sessions.subjects.vcr-schedule');
        Route::get('/subjects/{subject}/live-sessions', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@subjectLiveSessions')->name('vcr-sessions.subjects.live-sessions');
        Route::get('/subjects/{subject}/courses', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@subjectCourses')->name('vcr-sessions.subjects.courses');
        Route::get('/courses/attendance/{course}', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@courseVCRAttendance')->name('vcr-sessions.courses.attendance');
        Route::get('/courses/attendance/{course}/export', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@courseVCRAttendanceExport')->name('vcr-sessions.courses.attendance.export');
        Route::get('/vcrSchedules/attendance/{vcrSchedule}', '\App\OurEdu\VCRSessions\Admin\Controllers\VCRStudentsController@scheduleVCRAttendance')->name('vcr-sessions.schedules.attendance');
    });
