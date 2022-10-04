<?php
Route::group(['prefix'=>'grade-classes','as'=>'gradeClasses.'], function () {

    Route::get('/', '\App\OurEdu\GradeClasses\Admin\Controllers\GradeClassControllers@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\GradeClasses\Admin\Controllers\GradeClassControllers@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\GradeClasses\Admin\Controllers\GradeClassControllers@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\GradeClasses\Admin\Controllers\GradeClassControllers@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\GradeClasses\Admin\Controllers\GradeClassControllers@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\GradeClasses\Admin\Controllers\GradeClassControllers@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\GradeClasses\Admin\Controllers\GradeClassControllers@delete')->name('delete');

});
