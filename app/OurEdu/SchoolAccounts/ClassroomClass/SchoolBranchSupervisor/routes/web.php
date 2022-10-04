<?php
Route::group(['prefix'=>'classrooms','as'=>'classrooms.'], function () {
    Route::group(['prefix'=>'{classroom}/classroom-class','as'=>'classroomClasses.'], function () {
        Route::get('/all/{branch?}', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@getIndex')->name('index');
        Route::get('/timetable', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@timetable')->name('get.timetable');
        Route::get('/{classroomClass}/view', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@getView')->name('view');
        Route::get('/create', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@getCreate')->name('get.create');
        Route::post('/create', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@postCreate')->name('post.create');
        Route::get('/import', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@getImport')->name('get.import');
        Route::post('/import', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@uploadExcel')->name('post.import');
//        Route::get('{classroomClass}/edit', '\App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers\ClassroomClassController@getEdit')->name('get.edit');
//        Route::post('{classroomClass}/edit', '\App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers\ClassroomClassController@postEdit')->name('post.edit');
        Route::get('{classroomClass}/delete', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@delete')->name('delete');
    });
    Route::get('session/url/{session_id}/{classroom_id}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getSessionUrlForSuperVisor')->name('classroomClasses.getSessionUrlForSuperVisor');
    Route::get('/import/download/{job}', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@downloadExcel')->name('import.download');
    Route::get('/import/errors/{job}', '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\ClassroomClassController@showImportJobErrors')->name('import.errors');
    Route::get('get-subject-instructors/{branch?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getSubjectInstructors')->name('classroomClasses.ajax.getSubjectInstructors');
    Route::get('get-subject-instructors' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getSubjectInstructors')->name('classroomClasses.getSubjectInstructors');
    Route::get('get-instructor-subjects/{instructor?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getInstructorSubjects')->name('getInstructorSubjects');
    Route::get('get-grade-class-subjects/{gradeClass?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getGradeSubjects')->name('get.grade.class.subjects');
    Route::get('get-classroom/{branch?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getBranchClassrooms')->name('branchClassroom.byBranch');
    Route::get('get-grade-classroom/{gradeClass?}/{branch?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@geGradeClassrooms')->name('gradeClass.Classroom');
    Route::get('get-branch.grades/{branch?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getBranchGrade')->name('branches.gradeClass');
    Route::get('get-classroom-instructor/{classroom?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@classroomInstructors')->name('getInstructor');
    Route::get('get-classroom-instructor-sessions' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@classroomInstructorSessions')->name('instructor.sessions');
    Route::get('get-classroom-classes/{classroom?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getClassroomClasses')->name('classroomClasses.byClassroom');
    Route::get('get-classroom-class-sessions/{class?}' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getClassSessions')->name('classroomClasses.sessions');
    Route::get('get-classroom-students' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getClassroomStudents')->name('classroomClasses.getClassroomStudents');
    Route::get('{id}/timetable' , '\App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers\AjaxController@getClassRoomTimetable')->name('classroomClasses.getClassRoomTimetable');
});
