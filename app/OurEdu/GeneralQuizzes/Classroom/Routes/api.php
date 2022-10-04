<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "classrooms", 'namespace' => '\App\OurEdu\GeneralQuizzes\Classroom\Controllers' , 'as' => 'classroom.'], function () {
    Route::get("{classroom}/students", "ClassroomController@getClassroomStudents");
});
