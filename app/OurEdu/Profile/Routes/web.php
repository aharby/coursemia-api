<?php

Route::group(['prefix' => 'profile','as'=>'profile.'], function () {
    Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
        require base_path('app/OurEdu/Profile/Admin/Routes/web.php');
    });
    Route::get('/logout', '\App\OurEdu\Profile\Controllers\ProfileController@getLogout')->name('get.logout');

    require base_path('app/OurEdu/Profile/SchoolAccounts/Routes/web.php');
});
