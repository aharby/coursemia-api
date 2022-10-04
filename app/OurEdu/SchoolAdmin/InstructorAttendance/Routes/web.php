<?php

use Illuminate\Support\Facades\Route;


Route::group([
        "prefix" => "reports",
        "as" => "reports.",
        "namespace" => "\App\OurEdu\SchoolAdmin\InstructorAttendance\Controllers"
    ], function () {

        Route::get("students/class/presence/{branch?}", "\App\OurEdu\SchoolAccounts\Reports\Controllers\StudentsPresenceReportController@classPresence")->name("students.class.presence");
        Route::get("students/classroom/presence/export/{branch?}", "\App\OurEdu\SchoolAccounts\Reports\Controllers\StudentsPresenceReportController@exportClassPresence")->name("students.class.presence.export");
        Route::get("students/subjects/presence/{branch?}", "\App\OurEdu\SchoolAccounts\Reports\Controllers\StudentsPresenceReportController@subjectsPresence")->name("students.subjects.presence");
        Route::get("students/subjects/presence/Export/{branch?}", "\StudentsPresenceReportController@ExportSubjectsPresence")->name("students.subjects.presence.export");
        Route::get('instructors/sessions/attendance', '\App\OurEdu\SchoolAdmin\InstructorAttendance\Controllers\TeacherAttendanceController@teacherSessionAttendance')->name('instructor.sessions.attendance');
        Route::get('instructors/sessions/attendance/export', '\App\OurEdu\SchoolAdmin\InstructorAttendance\Controllers\TeacherAttendanceController@exportTeacherSessionAttendance')->name('instructor.sessions.attendance.export');
    });

    
