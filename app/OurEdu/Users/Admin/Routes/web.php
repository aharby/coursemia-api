<?php

Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/', '\App\OurEdu\Users\Admin\Controllers\UsersController@getIndex')->name('get.index');

    Route::get('/create', '\App\OurEdu\Users\Admin\Controllers\UsersController@getCreate')->name('get.create');
    Route::post('/create', '\App\OurEdu\Users\Admin\Controllers\UsersController@postCreate')->name('post.create');

    Route::get('/edit/{id}', '\App\OurEdu\Users\Admin\Controllers\UsersController@getEdit')->name('get.edit');
    Route::put('/edit/{id}', '\App\OurEdu\Users\Admin\Controllers\UsersController@postEdit')->name('put.edit');

    Route::get('/view/{id}', '\App\OurEdu\Users\Admin\Controllers\UsersController@getView')->name('get.view');

    Route::get('/logs/{id}', '\App\OurEdu\Users\Admin\Controllers\UserLogsController@listUserLogs')->name('get.logs');

    Route::get('/logs/view/{id}', '\App\OurEdu\Users\Admin\Controllers\UserLogsController@viewUserLog')->name('get.viewLog');

    Route::get('/students/{id}', '\App\OurEdu\Users\Admin\Controllers\UsersController@editStudents')->name('get.students');
    Route::put('/students/{id}', '\App\OurEdu\Users\Admin\Controllers\UsersController@updateStudents')->name('put.students');

    Route::delete('/delete/{id}', '\App\OurEdu\Users\Admin\Controllers\UsersController@getDelete')->name('delete');
    Route::post('/suspend/{id}', '\App\OurEdu\Users\Admin\Controllers\UsersController@getSuspend')->name('suspend');

    // student & student-teacher routes
    Route::get('/student-student-teacher/{studentId}', '\App\OurEdu\Users\Admin\Controllers\UsersController@indexStudentStudentTeachers')
        ->name('index.student.student-teacher');

    Route::get('/add-student-teacher/{studentId}', '\App\OurEdu\Users\Admin\Controllers\UsersController@getAddStudentTeacherToStudent')
        ->name('get.add.student-teacher');

    Route::post('/add-student-teacher/{studentId}', '\App\OurEdu\Users\Admin\Controllers\UsersController@postAddStudentTeacherToStudent')
        ->name('post.add.student-teacher');

    Route::delete('/detach-student-teacher/{studentId}/{studentTeacherId}', '\App\OurEdu\Users\Admin\Controllers\UsersController@detachStudentTeacherFromStudent')
        ->name('detach.student-teacher');

    // AJAX Routes
    Route::get('/get-instructors', '\App\OurEdu\Users\Admin\Controllers\AjaxUsersController@getInstructors')->name('get.instructors');

    Route::get('/search-students', '\App\OurEdu\Users\Admin\Controllers\AjaxUsersController@searchStudents')->name('get.searchStudents');
});
