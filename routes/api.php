<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::group(['as' => 'api.'], function () {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        require base_path('app/OurEdu/Users/Auth/Routes/api.php');
    });

    require base_path('app/OurEdu/LandingPage/Routes/api.php');
    require base_path('app/OurEdu/GarbageMedia/Routes/api.php');
    require base_path('app/OurEdu/AppVersions/Routes/api.php');
    require base_path('app/OurEdu/LookUp/Routes/api.php');
    require base_path('app/OurEdu/GeneralQuizzes/Routes/api.php');
    require base_path('app/OurEdu/Assessments/routes/api.php');
    require base_path('app/OurEdu/Payments/Parent/Routes/apiPublic.php');

    //SME user routes
    Route::group(['prefix' => 'sme', 'as' => 'sme.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/Subjects/SME/Routes/api.php');
        require base_path('app/OurEdu/QuestionReport/SME/Routes/api.php');
        require base_path('app/OurEdu/Reports/SME/Routes/api.php');
        require base_path('app/OurEdu/GeneralExams/SME/Routes/api.php');
        require base_path('app/OurEdu/GeneralExamReport/SME/Routes/api.php');
    });

    //content-author user routes
    Route::group(['prefix' => 'content-author', 'as' => 'contentAuthor.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/Subjects/ContentAuthor/Routes/api.php');
        require base_path('app/OurEdu/QuestionReport/ContentAuthor/Routes/api.php');
    });

    //instructor user routes
    Route::group(['prefix' => 'instructor', 'as' => 'instructor.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/VCRSessions/Instructor/Routes/api.php');
        require base_path('app/OurEdu/Exams/Instructor/Routes/api.php');
        require base_path('app/OurEdu/Courses/Instructor/Routes/api.php');
        require base_path('app/OurEdu/SchoolAccounts/ClassroomClassSessions/Instructor/routes/api.php');
        //        require base_path('app/OurEdu/SchoolAccounts/ClassroomClass/Instructor/routes/api.php');
        require base_path('app/OurEdu/SchoolAccounts/SessionPreparations/SchoolInstructor/Routes/api.php');
    });

    //Student user routes
    Route::group(['prefix' => 'student', 'as' => 'student.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/Subjects/Student/Routes/api.php');
        require base_path('app/OurEdu/Reports/Student/Routes/api.php');
        require base_path('app/OurEdu/Exams/Student/Routes/api.php');
        require base_path('app/OurEdu/Exams/Student/Routes/api.practice.php');
        require base_path('app/OurEdu/Exams/Student/Routes/api.competition.php');
        require base_path('app/OurEdu/Exams/Student/Routes/api.instructor.competition.php');
        //        require base_path('app/OurEdu/Invitations/Student/Routes/api.php');
        require base_path('app/OurEdu/Feedbacks/Student/Routes/api.php');
        require base_path('app/OurEdu/Courses/Student/Routes/api.php');
        require base_path('app/OurEdu/SubjectPackages/Student/Routes/api.php');
        require base_path('app/OurEdu/PsychologicalTests/Student/Routes/api.php');
        require base_path('app/OurEdu/VCRSchedules/Student/Routes/api.php');
        require base_path('app/OurEdu/GeneralExams/Student/Routes/api.php');
        require base_path('app/OurEdu/LearningPerformance/Student/Routes/api.php');
        require base_path('app/OurEdu/VCRSessions/Student/Routes/api.php');
        require base_path('app/OurEdu/Quizzes/Student/Routes/api.homework.php');
        require base_path('app/OurEdu/Quizzes/Student/Routes/api.periodic-test.php');
        require base_path('app/OurEdu/Quizzes/Student/Routes/api.quiz.php');
        require base_path('app/OurEdu/Quizzes/Routes/api.quiz.php');
        require base_path('app/OurEdu/SchoolAccounts/ClassroomClassSessions/Student/routes/api.php');
        require base_path('app/OurEdu/SchoolAccounts/SessionPreparations/Student/Routes/api.php');
        require base_path('app/OurEdu/GeneralQuizzes/Student/Routes/api.php');
        require base_path('app/OurEdu/Exams/Student/Routes/api.course.competition.php');
    });

    //Parent user routes
    Route::group(['prefix' => 'parent', 'as' => 'parent.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/Payments/Parent/Routes/api.php');
        require base_path('app/OurEdu/Subscribes/Parent/Routes/api.php');
        require base_path('app/OurEdu/Subjects/Parent/Routes/api.php');
        require base_path('app/OurEdu/Courses/Parent/Routes/api.php');
        require base_path('app/OurEdu/LearningPerformance/Parent/Routes/api.php');
        require base_path('app/OurEdu/Quizzes/Parent/Routes/api.php');
        require base_path('app/OurEdu/Reports/Parent/Routes/api.php');
    });

    //Instructor user routes
    Route::group(['prefix' => 'instructor', 'as' => 'instructor.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/VCRSchedules/Instructor/Routes/api.php');
        require base_path('app/OurEdu/Subjects/Instructor/Routes/api.php');
        require base_path('app/OurEdu/Certificates/Instructor/Routes/api.php');
    });

    //student-teacher user routes
    Route::group(['prefix' => 'student-teacher', 'as' => 'studentTeacher.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/LearningPerformance/StudentTeacher/Routes/api.php');
    });

    //Educational Supervisor user routes
    Route::group(['prefix' => 'educational-supervisor', 'as' => 'educational-supervisor.', 'middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/SchoolAccounts/SessionPreparations/EducationalSupervisor/Routes/api.php');
        require base_path('app/OurEdu/SchoolAccounts/Classroom/EducationalSupervisor/routes/api.php');
        require base_path('app/OurEdu/SchoolAccounts/ClassroomClassSessions/EducationalSupervisor/routes/api.php');
    });

    require base_path('app/OurEdu/LearningResources/Routes/api.php');
    require base_path('app/OurEdu/Invitations/Routes/api.php');

    Route::group(['middleware' => ['auth:api']], function () {
        require base_path('app/OurEdu/Profile/Routes/api.php');
        require base_path('app/OurEdu/Exams/User/Routes/api.php');
        require base_path('app/OurEdu/Notifications/Routes/api.php');
        require base_path('app/OurEdu/Quizzes/Routes/api.homework.php');
        require base_path('app/OurEdu/Quizzes/Routes/api.periodic-test.php');
        require base_path('app/OurEdu/Quizzes/Routes/api.quiz.php');
        require base_path('app/OurEdu/Courses/Discussion/Routes/api.php');
    });
    // Static Pages
    require base_path('app/OurEdu/StaticPages/Routes/api.php');
    require base_path('app/OurEdu/Contact/Routes/api.php');

    require base_path('app/OurEdu/VCRSessions/General/Routes/api.php');

    require base_path('app/OurEdu/SchoolAccounts/SchoolRequests/Routes/api.php');

    //test notifications
    Route::post('log', '\App\Http\Controllers\LogsController@storeLog');
    Route::get('env', '\App\Http\Controllers\LogsController@envServer');
    Route::post('notifications/send', '\App\OurEdu\Notifications\Controllers\Api\NotificationApiController@sendNotification');
    Route::post('notifications/email/send', '\App\OurEdu\Notifications\Controllers\Api\NotificationApiController@senTestEmail');

    // test tasks
    Route::get('subject/{subjectId}/tasks', '\App\OurEdu\Subjects\SME\Controllers\Api\TaskApiController@getAllTasksForTest');

    Route::group(
        ['prefix' => 'general-quizzes', 'as' => 'general-quizzes.'],
        function () {
            Route::get("subject/ouredu/subsections/{section}", "\App\OurEdu\GeneralQuizzes\Subject\Controllers\SubjectSectionsController@subsections");
            Route::get("subject/ouredu/{subjectUuid}/sections", "\App\OurEdu\GeneralQuizzes\Subject\Controllers\SubjectSectionsController@OurEduSubjectSections");
        }
    );
});
Route::post('/mail','\App\OurEdu\BaseNotification\Controller\SendController@sendMail');
require base_path('app/OurEdu/VideoCall/Routes/api.php');
