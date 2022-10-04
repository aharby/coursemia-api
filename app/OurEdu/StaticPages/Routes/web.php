<?php
Route::group(['prefix'=>'static-pages','as'=>'staticPages.'], function () {
    Route::get('/', '\App\OurEdu\StaticPages\Controllers\StaticPagesController@getIndex')->name('get.index');
    Route::get('/{id}/view', '\App\OurEdu\StaticPages\Controllers\StaticPagesController@getView')->name('get.view');
    Route::get('/{id}/edit', '\App\OurEdu\StaticPages\Controllers\StaticPagesController@getEdit')->name('get.edit');
    Route::put('/{id}/edit', '\App\OurEdu\StaticPages\Controllers\StaticPagesController@postEdit')->name('post.edit');
});
