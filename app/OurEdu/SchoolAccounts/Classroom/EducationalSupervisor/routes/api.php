<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'classrooms',
    'as' => 'classrooms.',
    'namespace' =>  '\App\OurEdu\SchoolAccounts\Classroom\EducationalSupervisor\Controllers\Api'
], function () {
    Route::get("/", "ClassroomController@index");
});
