<?php
Route::group(['prefix'=>'static-pages','as'=>'staticPages.'], function () {
    Route::get('/', '\App\Modules\StaticPages\Controllers\StaticPagesController@getIndex')->name('get.index');
    Route::get('/{id}/view', '\App\Modules\StaticPages\Controllers\StaticPagesController@getView')->name('get.view');
    Route::get('/{id}/edit', '\App\Modules\StaticPages\Controllers\StaticPagesController@getEdit')->name('get.edit');
    Route::put('/{id}/edit', '\App\Modules\StaticPages\Controllers\StaticPagesController@postEdit')->name('post.edit');
});
