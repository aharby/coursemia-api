<?php

use Illuminate\Support\Facades\Route;

Route::group(['as' => 'v2.api.'], function () {

    //instructor user routes
    Route::group(['prefix' => 'instructor', 'as' => 'instructor.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/SchoolAccounts/ClassroomClassSessions/Instructor/routes/v2/api.php');
        require base_path('app/OurEdu/Courses/Instructor/Routes/V2/api.php');
    });

    Route::group(['prefix' => 'student', 'as' => 'student.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/SchoolAccounts/ClassroomClassSessions/Student/routes/v2/api.php');
    });


    require base_path('app/OurEdu/VCRSessions/General/Routes/V2/api.php');

});
