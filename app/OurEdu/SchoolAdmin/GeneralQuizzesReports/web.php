<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'general-quizzes-reports',
    'as' => 'general-quizzes-reports.',
], function () {
    require base_path('app/OurEdu/SchoolAdmin/GeneralQuizzesReports/TotalPercentage/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/GeneralQuizzesReports/StudentsReports/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/GeneralQuizzesReports/BranchesReports/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/GeneralQuizzesReports/FormativeTest/Routes/web.php');
});
