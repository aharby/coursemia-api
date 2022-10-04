<?php

Route::group(['prefix'=>'school/account','as'=>'school.account.'],function (){
    Route::get("profile/edit", "\App\OurEdu\Profile\SchoolAccounts\Controllers\ProfileController@edit")->name("profile.edit");
    Route::put("profile/update", "\App\OurEdu\Profile\SchoolAccounts\Controllers\ProfileController@update")->name("profile.update");
    Route::put("profile/change/password", "\App\OurEdu\Profile\SchoolAccounts\Controllers\ProfileController@changePassword")->name("profile.change.password");
});
