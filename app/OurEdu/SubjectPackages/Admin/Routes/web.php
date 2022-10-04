<?php
Route::group(['prefix'=>'subject-packages','as'=>'subjectPackages.'], function () {

    Route::get('/', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesController@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesController@delete')->name('delete');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\SubjectPackages\Admin\Controllers\SubjectPackagesLogsController@listSubjectPackagesLogs')->name('get.logs');


});
