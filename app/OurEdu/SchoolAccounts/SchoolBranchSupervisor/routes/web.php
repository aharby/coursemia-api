<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'sessions/preparations',
    'as'=>'session.preparation.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers'
], function () {
    Route::get('media/library', 'SessionPreparationController@getMediaLibrary')->name('get.media.library');
    Route::get('media/single/{media}', 'SessionPreparationController@getSingleMedia')->name('get.single.media');
    Route::get('/view/{session}', 'SessionPreparationController@getSessionPreparation')->name('get.view.preparation');
});
Route::group([
    'prefix'=>'educational-supervisors',
    'as'=>'educational-supervisors.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers'
], function () {
    Route::get('all/{branch?}', 'EducationalSupervisorController@getIndex')->name('get.index');
    Route::get('/edit/{educational_supervisor}/{branch?}', 'EducationalSupervisorController@edit')->name('get.edit');
    Route::put('/edit/{educational_supervisor}/{branch?}', 'EducationalSupervisorController@update')->name('put.edit');
    Route::get('/view/{educational_supervisor}', 'EducationalSupervisorController@getview')->name('get.view');

    Route::get('get-branch-education-systems/{branch?}', 'AjaxController@getBranchEducationalSystem')->name('getEducationalSystemsByBranch');
    Route::get('get-education-system-gradeclasses/{educational_system?}', 'AjaxController@getGradeClassesByEducationalSystem')->name('getGradeClassesByEducationalSystem');
    Route::get('get-gradeclass-subjects/{grade_classes?}', 'AjaxController@getSubjectsByGradeClass')->name('getSubjectsByGradeClass');

});
