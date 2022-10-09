<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api','userActive'], 'namespace' => '\App\Modules\Courses\Controllers'], function (){
    Route::post('get-courses', 'CoursesAPIController@courses');
    Route::get('my-courses', 'CoursesAPIController@myCourses');
});
