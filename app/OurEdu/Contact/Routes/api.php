<?php
Route::group(['prefix'=>'contact','as'=>'contact.'], function () {
    Route::post('/create', '\App\OurEdu\Contact\Controllers\ContactApiController@postCreate')->name('post.create');
});
