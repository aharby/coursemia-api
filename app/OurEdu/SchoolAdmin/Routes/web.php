<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'school-admin',
    'as' => 'school-admin.',
    'middleware' => 'auth',
], function () {
    require base_path('app/OurEdu/SchoolAdmin/SchoolAccountBranches/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/InstructorAttendance/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/AttendanceReports/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/GeneralQuizzes/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/GeneralQuizzesReports/web.php');
    require base_path('app/OurEdu/SchoolAdmin/MediaLibrary/Routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/Profile/Routes/web.php');
    require base_path('app/OurEdu/Assessments/SchoolAdmin/routes/web.php');
    require base_path('app/OurEdu/SchoolAdmin/FormativeTest/Routes/web.php');
  
});
