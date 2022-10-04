<?php

Route::group([
    'prefix'=>'grade-classes',
    'as'=>'grade-classes.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\GradeClasses\SchoolBranchSupervisor\Controllers'
], function () {
    Route::get('/{branch?}', 'GradeClassesController@getIndex')->name('get.index');
    Route::get('/educational-systems/{gradeClassId}/{branch?}', 'GradeClassesController@getEducationalSystems')->name('get.educational-systems');
    Route::get('/subjects/{gradeClassId}/{branchEducationalSystemId}/{branch?}', 'GradeClassSubjectController@getSubjects')->name('get.subjects');

});
