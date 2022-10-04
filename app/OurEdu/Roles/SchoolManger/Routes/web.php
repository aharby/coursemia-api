<?php
Route::group(['prefix'=>'roles','as'=>'roles.'], function () {
        Route::get('/', '\App\OurEdu\Roles\SchoolManger\Controller\RolesController@getIndex')->name('index');

    Route::get('/create', '\App\OurEdu\Roles\SchoolManger\Controller\RolesController@getCreate')->name('get.create');
    Route::post('/create', '\App\OurEdu\Roles\SchoolManger\Controller\RolesController@postCreate')->name('post.create');

    Route::get('/edit/{role}', '\App\OurEdu\Roles\SchoolManger\Controller\RolesController@getEdit')->name('get.edit');
    Route::post('/edit/{role}', '\App\OurEdu\Roles\SchoolManger\Controller\RolesController@postEdit')->name('post.edit');

    Route::delete('/delete/{role}', '\App\OurEdu\Roles\SchoolManger\Controller\RolesController@deleteRole');
});
