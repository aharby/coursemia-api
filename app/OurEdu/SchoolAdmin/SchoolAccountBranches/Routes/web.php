<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'school-account-branches',
    'as'=>'school-account-branches.',
    'namespace'=>'\App\OurEdu\SchoolAdmin\SchoolAccountBranches\Controllers',
], function () {
    Route::get('/', 'SchoolAccountBranchesController@getIndex')->name('get.index');
    Route::get('view/{branchId}','SchoolAccountBranchesController@getView')->name('getView');
    Route::get('edit/{id}', 'SchoolAccountBranchesController@getEdit')->name('get.edit');
    Route::put('edit/{id}', 'SchoolAccountBranchesController@putEdit')->name('put.edit');

    Route::get('branch-subjects/{branch}', 'BranchSubjectsController@index')->name('branch.subjects.index');
    Route::post('branch-questions-branch-subjects-permissions/{subject}', 'BranchSubjectsController@questionsPermissionsBank')->name('branch.questions.branch.subjects.permissions');


});