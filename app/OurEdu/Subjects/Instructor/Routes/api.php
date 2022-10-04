<?php
Route::group(['prefix' => 'subjects', 'as' => 'subjects.'], function () {

    Route::get('/{subjectId}/sections', '\App\OurEdu\Subjects\Instructor\Controllers\Api\SubjectApiController@viewSubjectSections')->name('view-subject-sections');
});
