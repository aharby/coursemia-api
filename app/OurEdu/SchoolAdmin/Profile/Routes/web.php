<?php

//Route::group(['prefix' => 'school_admin/profile', 'as' => 'school-admin.'], function () {
//    Route::get("/edit", "ProfileController@edit")->name("profile.edit");
//    Route::put("/update", "\App\OurEdu\SchoolAdmin\Profile\Controllers\ProfileController@update")->name("profile.update");
//    Route::put("/change/password", "\App\OurEdu\SchoolAdmin\Profile\Controllers\ProfileController@changePassword")->name("profile.change.password");
//});
//

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'profile',
    'as' => 'profile.',
    'namespace' => '\App\OurEdu\SchoolAdmin\Profile\Controllers',
], function () {
    Route::get("/edit", "ProfileController@edit")->name("edit");
    Route::put("/update", "ProfileController@update")->name("update");
    Route::put("/change/password", "ProfileController@edit")->name("change.password");
    Route::get('update_current_school/{school_id}', "ProfileController@updateCurrentSchool")->name('updateCurrentSchool');

});
