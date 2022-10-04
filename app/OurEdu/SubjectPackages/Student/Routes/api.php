<?php
Route::group(['prefix'=>'subject-packages','as'=>'subjectPackages.'], function () {

    Route::get('/', '\App\OurEdu\SubjectPackages\Student\Controllers\SubjectPackagesApiController@getAvailablePackages')->name('get.available.packages');

    Route::get('/list/{studentId}', '\App\OurEdu\SubjectPackages\Student\Controllers\SubjectPackagesApiController@listSubjectPackagesForStudent')->name('listSubjectPackagesForStudent');

    Route::get('/view-package/{packageId}/{studentId?}', '\App\OurEdu\SubjectPackages\Student\Controllers\SubjectPackagesApiController@viewPackage')->name('view.package');

    Route::post('/subscribe-package/{packageId}', '\App\OurEdu\SubjectPackages\Student\Controllers\SubjectPackagesApiController@postSubscribePackage')->name('post.subscribe.package');

});
