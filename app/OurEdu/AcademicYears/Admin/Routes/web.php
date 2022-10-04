<?php
Route::group(['prefix'=>'academic-years','as'=>'academicYears.'],function (){

    Route::get('/', '\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@getCreate')->name('get.create');
    Route::post('create/', '\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@getEdit')->name('get.edit');
    Route::put('edit/{id}', '\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@delete')->name('delete');

    Route::get('/get-educational-system','\App\OurEdu\AcademicYears\Admin\Controllers\AcademicYearsController@getEducationalSystem')->name('get.educational.system');

});
