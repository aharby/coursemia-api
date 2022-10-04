<?php

Route::group([], function () {
    Route::get('edit/', '\App\OurEdu\Profile\Admin\Controllers\ProfileController@getEdit')->name('get.edit');
    Route::put('edit/', '\App\OurEdu\Profile\Admin\Controllers\ProfileController@postEdit')->name('post.edit');

});
