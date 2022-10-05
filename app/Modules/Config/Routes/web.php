<?php
Route::group(['prefix' => 'configs', 'as' => 'configs.'], function () {
    Route::get('/edit', '\App\Modules\Config\Controllers\ConfigsController@getEdit')->name('get.edit');
    Route::put('/edit', '\App\Modules\Config\Controllers\ConfigsController@postEdit')->name('post.edit');
});
