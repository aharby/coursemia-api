<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'school-account-branches',
    'as'=>'school-account-branches.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers',
], function () {
    Route::get('/', 'SchoolAccountBranchesController@getIndex')->name('get.index');
    Route::get('edit/{id}', 'SchoolAccountBranchesController@getEdit')->name('get.edit');
    Route::put('edit/{id}', 'SchoolAccountBranchesController@putEdit')->name('put.edit');
    Route::get('/update-passwords/{user}', 'SchoolAccountBranchesController@getUpdatePassword')->name('get-update-password');
    Route::get('/update/{user}', 'SchoolAccountBranchesController@getUpdateUser')->name('get-update');
    Route::post('/update-passwords', 'SchoolAccountBranchesController@postUpdatePassword')->name('post-update-password');
    Route::post('/update', 'SchoolAccountBranchesController@postUpdate')->name('post-update');
    Route::get('view/{id}', 'SchoolAccountBranchesController@getView')->name('get.view');
    Route::delete('delete/{id}', 'SchoolAccountBranchesController@delete')->name('delete');
    Route::get('/users', 'SchoolAccountBranchesController@getUsers')->name('get.users');

    Route::get('/set-role/{id}', 'SchoolAccountBranchesController@getSetRole')->name('get-set-role');
    Route::post('/set-role/{id}', 'SchoolAccountBranchesController@postSetRole')->name('post-set-role');

    Route::get('branch-subjects/{branch}', 'BranchSubjectsController@index')->name('branch.subjects.index');
    Route::post('branch-questions-branch-subjects-permissions/{subject}', 'BranchSubjectsController@questionsPermissionsBank')->name('branch.questions.branch.subjects.permissions');

});


Route::group([
    'prefix'=>'branch-grade-classes',
    'as'=>'branch-grade-classes.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers',
], function () {
    Route::get('/', 'AssignBranchesController@getIndex')->name('get.index');
    Route::get('edit/{id}/{educationalSystemId}', 'AssignBranchesController@getEdit')->name('get.edit');
    Route::post('assign', 'AssignBranchesController@assignGradeClasses')->name('assign-grade-classes');
    Route::get('view/{id}', 'SchoolAccountBranchesController@getView')->name('get.view');
    Route::delete('delete/{id}', 'SchoolAccountBranchesController@delete')->name('delete');
});

Route::group([
    'prefix' => 'users',
    'as' => 'users.',
    'namespace' => '\App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers'
], function () {
    Route::get("/{user}/show", "SchoolAccountUsersController@show")->name("view");
    Route::get("/create", "SchoolAccountUsersController@create")->name("create");
    Route::post("/", "SchoolAccountUsersController@store")->name("store");
});

Route::group([
    'prefix'=>'sessions/preparations',
    'as'=>'session.preparation.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers'
], function () {
    Route::get('media/library', 'SessionPreparationController@getMediaLibrary')->name('get.media.library');
    Route::get('media/single/{media}', 'SessionPreparationController@getSingleMedia')->name('get.single.media');
});


Route::group([
    'prefix' => 'manager-reports',
    'as' => 'manager-reports.',
    'namespace' => '\App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers'
], function () {
    Route::get("/user-attends", "SchoolAccountReportsController@getUserAttends")->name("user-attends");
    Route::get("/user-attends/export", "SchoolAccountReportsController@exportUserAttends")->name("user-attends.export");
    Route::get("/user-attends/sessions/export/{user}", "SchoolAccountReportsController@exportUserPresenceSessions")->name("user-attends.sessions.export");
    Route::get('/classroom-subjects','AjaxController@getClassroomSubjects')->name('get-classroom-subjects');
    Route::get('/branch-subjects/{branch?}','AjaxController@getBranchSubject')->name('get-branch-subjects');
    Route::get('get-grade-subjects/{gradeClass?}/{branch?}' , 'AjaxController@getGradeSubjects')->name('gradeClass.subjects');
    Route::get('/branch-quiz-creators/{branch?}','AjaxController@getBranchQuizCreator')->name('get-branch-quiz-creators');
});
