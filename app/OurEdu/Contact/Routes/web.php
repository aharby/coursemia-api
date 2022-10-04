<?php
Route::group(['prefix'=>'contact','as'=>'contact.'], function () {
    Route::get('/', '\App\OurEdu\Contact\Controllers\ContactController@getIndex')->name('get.index');
    Route::get('/view/{contact}', '\App\OurEdu\Contact\Controllers\ContactController@view')->name('get.view');
});
