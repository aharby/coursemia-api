<?php

use Illuminate\Support\Facades\Route;


Route::group([
        "prefix" => "reports",
        "as" => "reports.",
        "namespace" => "\App\OurEdu\SchoolAccounts\Reports\Controllers"
    ], function () {
        Route::get("students/class/presence/{branch?}", "StudentsPresenceReportController@classPresence")->name("students.class.presence");
        Route::get("students/classroom/presence/export/{branch?}", "StudentsPresenceReportController@exportClassPresence")->name("students.class.presence.export");
        Route::get("students/subjects/presence/{branch?}", "StudentsPresenceReportController@subjectsPresence")->name("students.subjects.presence");
        Route::get("students/subjects/presence/Export/{branch?}", "StudentsPresenceReportController@ExportSubjectsPresence")->name("students.subjects.presence.export");
        Route::get('instructors/sessions/attendance', 'TeacherAttendanceController@teacherSessionAttendance')->name('instructor.sessions.attendance');
        Route::get('instructors/sessions/attendance/export', 'TeacherAttendanceController@exportTeacherSessionAttendance')->name('instructor.sessions.attendance.export');
    });
