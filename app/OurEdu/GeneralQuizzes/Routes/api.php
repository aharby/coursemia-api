<?php

use Illuminate\Support\Facades\Route;
Route::group(
    ['prefix' => 'general-quizzes', 'as' => 'general-quizzes.', 'middleware' => ['auth:api']],
    function () {
        Route::group(
            ['prefix' => 'homework', 'as' => 'homework.'],
            function () {
                require base_path('app/OurEdu/GeneralQuizzes/Homework/Instructor/Routes/api.php');
                require base_path('app/OurEdu/GeneralQuizzes/Homework/Student/Routes/api.php');
                require base_path('app/OurEdu/GeneralQuizzes/Homework/EducationalSupervisor/Routes/api.php');
            }
        );
        Route::group(
            ['prefix' => 'periodic-test', 'as' => 'periodic-test.'],
            function () {
                require base_path('app/OurEdu/GeneralQuizzes/PeriodicTest/Instructor/Routes/api.php');
                require base_path('app/OurEdu/GeneralQuizzes/PeriodicTest/Student/Routes/api.php');
                require base_path('app/OurEdu/GeneralQuizzes/PeriodicTest/EducationalSupervisor/Routes/api.php');
            }
        );

        require base_path('app/OurEdu/GeneralQuizzes/EducationalSupervisor/Routes/api.php');
        require base_path('app/OurEdu/GeneralQuizzes/Classroom/Routes/api.php');
        require base_path('app/OurEdu/GeneralQuizzes/Subject/Routes/api.php');
        require base_path('app/OurEdu/GeneralQuizzes/Parent/Routes/api.php');

        Route::get("look-up", "\App\OurEdu\GeneralQuizzes\Lookup\Controllers\Api\LookUpController@index");

        require base_path('app/OurEdu/GeneralQuizzes/QuestionTypes/Routes/api.php');

        Route::group(
            ['prefix' => 'course-homework', 'as' => 'course-homework.'],
            function () {
                require base_path('app/OurEdu/GeneralQuizzes/CourseHomework/Instructor/Routes/api.php');
                require base_path('app/OurEdu/GeneralQuizzes/CourseHomework/Student/Routes/api.php');
            }
        );
    }
);
