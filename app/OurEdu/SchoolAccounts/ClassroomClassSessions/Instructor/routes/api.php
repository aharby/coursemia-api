<?php
Route::group(['prefix'=>'classroom-class','as'=>'classroomClasses.'], function () {
    Route::get('/', '\App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Controllers\Api\ClassroomClassController@getIndex')->name('index');
    Route::get('/{sessionId}/students', '\App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Controllers\Api\ClassroomClassController@getSessionStudents')->name('get-session-students');
    Route::post('/{sessionId}/score-results/{studentId}','\App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Controllers\Api\ClassroomClassController@scoreStudentSessionResult')->name('post-student-session-result');
});
