<?php


Route::group([
    'prefix'=>'subject-instructors',
    'as'=>'subject-instructors.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Controllers'
], function () {
    Route::get('/all/{branch?}', 'SubjectInstructorsController@getSchoolInstructors')->name('get.school-instructor');
    Route::post('/import-subject-instructors/{subjectId}/{branch?}', 'SubjectInstructorsController@importSubjectInstructors')->name('import');
    Route::get('/update-instructor-password/{id}', 'SubjectInstructorsController@getUpdateInstructorPassword')->name('get-update-instructor-password');
    Route::post('/update-instructor-password', 'SubjectInstructorsController@postUpdateInstructorPassword')->name('post-update-instructor-password');
    Route::get('/edit-instructor/{instructorUserId}/{branch?}', 'SubjectInstructorsController@getUpdateInstructor')->name('get-edit-instructor');
    Route::put('/edit-instructor/{instructorUserId}/{branch?}', 'SubjectInstructorsController@putUpdateInstructor')->name('put-edit-instructor');

    Route::get('/activate-instructor/{user}', 'SubjectInstructorsController@activeInstructor')->name('get-active-instructor');
    Route::get('view/{branch?}', 'SubjectInstructorsController@getView')->name('get.view');
    Route::get('export/{branch?}', 'SubjectInstructorsController@exportInstructors')->name('export.instructors.data');


});

Route::group([
    'prefix'=>'classrooms',
    'as'=>'classrooms.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers'
    ], function () {
    Route::get('/all/{branch?}', 'ClassroomController@getIndex')->name('get.index');
    Route::get("trashed", "ClassroomController@getTrashedClassrooms")->name("trashed");
    Route::get('/create/{branch?}', 'ClassroomController@getCreate')->name('get.create');
    Route::post('/create/{branch?}', 'ClassroomController@postCreate')->name('post.create');
    Route::get('/edit/{id}/{branch?}', 'ClassroomController@getEdit')->name('get.edit');
    Route::put('/edit/{id}/{branch?}', 'ClassroomController@putEdit')->name('put.edit');
    Route::get('view/{id}/{branch?}', 'ClassroomController@getView')->name('get.view');
    Route::get('delete/{id}', 'ClassroomController@delete')->name('delete');
});

Route::group([
    'prefix'=>'special-classrooms',
    'as'=>'specialClassrooms.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers'
    ], function () {
    Route::get('/', 'SpecialClassroomController@getIndex')->name('get.index');
    Route::get('/create', 'SpecialClassroomController@getCreate')->name('get.create');
    Route::post('/create', 'SpecialClassroomController@postCreate')->name('post.create');
    Route::get('/edit/{id}', 'SpecialClassroomController@getEdit')->name('get.edit');
    Route::put('/edit/{id}', 'SpecialClassroomController@putEdit')->name('put.edit');

    Route::get('students/{classroom}','SpecialClassroomStudentsController@index')->name('students');
});

Route::group([
    'prefix'=>'students',
    'as'=>'students.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers',
], function () {
    Route::get('/all/{branch?}', 'SchoolStudentsController@getIndex')->name('get.index');
    Route::get('/edit/{student}/{branch?}', 'SchoolStudentsController@edit')->name('get.edit');
    Route::put('/edit/{student}/{branch?}', 'SchoolStudentsController@update')->name('put.edit');
    Route::get('/export/{branch?}', 'SchoolStudentsController@exportStudents')->name('get.export');
    Route::get('/parents/all/{branch?}', 'SchoolStudentsController@getParents')->name('get.parents');
    Route::get('/parents/{student}/{parent}/delete', 'SchoolStudentsController@parentDelete')->name('parents.delete');
    Route::get('/parents/{student}/create', 'SchoolStudentsController@parentCreate')->name('parents.create');
    Route::post('/parents/{student}/store', 'SchoolStudentsController@parentStore')->name('parents.store');
    Route::get('/parents/{student}/{parent}/edit', 'SchoolStudentsController@parentEdit')->name('parents.edit');
    Route::put('/parents/{student}/{parent}/update', 'SchoolStudentsController@parentUpdate')->name('parents.update');
    Route::get('/parents/export/{branch?}', 'SchoolStudentsController@exportParents')->name('get.export-parents');
    Route::get('/view/{studentId}', 'SchoolStudentsController@getviewStudent')->name('get.view-student');
    Route::get('/activate-student/{studentId}', 'SchoolStudentsController@activeStudent')->name('get.active-student');


});


