<?php
Route::group(['prefix' => 'subjects', 'as' => 'subjects.'], function () {

    // to get the available subjects to him
    Route::get('/', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@getIndex')->name('get.index');

    Route::get('qudrat/', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@getQudratIndex')->name('get.qudratIndex');

    Route::post('/subscribe/{subjectId}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@postSubscribe')->name('post.subscribe');
    // like/unlike subject format subject
    Route::get('/like-unlike/{subjectId}/{subjectFormatId}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@likeUnLikeSubjectFormat')->name('like-unlike');

    Route::post('/view-resource/{resourceId}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@postUpdateProgressUseCase')->name('post.postUpdateProgressUseCase');
    // to get his subjects for parents
    Route::get('/list-subjects/{studentId}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@listSubjectsByParent')->name('get.list-subjects-by-parent');

    Route::get('/qudrat/list-subjects/{studentId}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@listQudratSubjectsByParent')->name('get.list-qudrat-subjects-by-parent');
    
    Route::get('/view-subject/{subjectId}/{studentId?}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@viewSubject')->name('view-subject');

    Route::get('/{subjectId}/media', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@viewSubjectMedia')->name('view-subject-media');

    Route::get('/view-subject-format-subject/{sectionId}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectFormatSubjectApiController@viewSubjectFormatSubjectDetails')->name('viewSubjectFormatSubjectDetails');

    Route::get('/subject-format-subject/{sectionId}/resource-subject-format-subject/{resourceID}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectFormatSubjectApiController@viewResourceSubjectFormatSubjectDetails')->name('viewResourceSubjectFormatSubjectDetails');

    Route::get('/breadcrumbs/{sectionId}', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectFormatSubjectApiController@viewBreadCrumbs')->name('view-breadcrumbs');

    Route::get('/{subjectId}/sections', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@viewSubjectSections')->name('view-subject-sections');

    Route::get('/section/{sectionId}/sections', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectApiController@viewSectionChildSections')->name('view-section-sections');

    Route::post('/set-time', '\App\OurEdu\Subjects\Student\Controllers\Api\SubjectTimesApiController@setSubjectTime')->name('setSubjectTime');
});
