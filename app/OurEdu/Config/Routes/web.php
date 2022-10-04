<?php
Route::group(['prefix' => 'configs', 'as' => 'configs.'], function () {
    Route::get('/edit', '\App\OurEdu\Config\Controllers\ConfigsController@getEdit')->name('get.edit');
    Route::put('/edit', '\App\OurEdu\Config\Controllers\ConfigsController@postEdit')->name('post.edit');
});
