<?php
Route::group(['prefix' => 'app-versions', 'as' => 'app.versions.'], function () {
    Route::get('/edit', '\App\OurEdu\AppVersions\Controllers\AppVersionController@getEdit')->name('get.edit');
    Route::put('/edit', '\App\OurEdu\AppVersions\Controllers\AppVersionController@postEdit')->name('post.edit');
});
