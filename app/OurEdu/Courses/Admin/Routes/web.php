<?php
Route::group(['prefix'=>'courses','as'=>'courses.'], function () {
    Route::get('/', '\App\OurEdu\Courses\Admin\Controllers\CourseController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\Courses\Admin\Controllers\CourseController@getCreate')->name('get.create');

    Route::post('create/', '\App\OurEdu\Courses\Admin\Controllers\CourseController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseController@getEdit')->name('get.edit');

    Route::put('edit/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseController@delete')->name('delete');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseLogsController@listCoursesLogs')->name('get.logs');
    //course sessions index
    Route::get('/sessions/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseController@getCourseSessions')->name('get.course.sessions');
    Route::get('{id}/session/create', '\App\OurEdu\Courses\Admin\Controllers\CourseController@getSession')->name('get.create.session');
    Route::post('{id}/session/create', '\App\OurEdu\Courses\Admin\Controllers\CourseController@postSession')->name('post.create.session');

});


Route::group(['prefix'=>'course-sessions','as'=>'courseSessions.'], function () {
    Route::get('/', '\App\OurEdu\Courses\Admin\Controllers\CourseSessionController@getIndex')->name('get.index');

    Route::get('edit/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseSessionController@getEdit')->name('get.edit');

    Route::put('edit/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseSessionController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseSessionController@getView')->name('get.view');

    Route::get('cancel/{id}', '\App\OurEdu\Courses\Admin\Controllers\CourseSessionController@cancel')->name('cancel');
});


Route::group(['prefix'=>'live-sessions','as'=>'liveSessions.'], function () {
    Route::get('create/', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@getCreate')->name('get.create');

    Route::post('create/', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@getEdit')->name('get.edit');

    Route::put('edit/{id}', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@delete')->name('delete');

    Route::get('/', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@getIndex')->name('get.index');

    //log
    Route::get('/logs/{id}', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionLogsController@listLiveSessionLogs')->name('get.sessions.logs');

    Route::get('cancel/{id}', '\App\OurEdu\Courses\Admin\Controllers\LiveSessionController@cancel')->name('cancel');

});
