<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

use App\OurEdu\Payments\Parent\Controllers\PaymentsApiController;
use Illuminate\Support\Facades\Route;

Route::get('test/{id}/details', '\App\OurEdu\Subjects\SME\Controllers\Api\SubjectApiController@getSubject')
    ->name('get.subjectDetails')->where('id', '[0-9]+');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    Route::group([], function () {
        Route::group(['as' => 'welcome.'], function () {
            Route::get('/', '\App\OurEdu\HomePage\Controllers\HomePageController@index')->name('index');
        });


        Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
            require base_path('app/OurEdu/Users/Auth/Routes/web.php');
        });

        Route::group(['middleware' => ['auth', 'suspended']], function () {
            require base_path('app/OurEdu/Notifications/Routes/web.php');
            require base_path('app/OurEdu/Users/Routes/web.php');
            require base_path('app/OurEdu/Dashboard/Routes/web.php');
            require base_path('app/OurEdu/Profile/Routes/web.php');
            require base_path('app/OurEdu/Countries/Routes/web.php');
            require base_path('app/OurEdu/EducationalSystems/Routes/web.php');
            require base_path('app/OurEdu/AcademicYears/Routes/web.php');

            Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
                Route::get('vcr-support-link', '\App\Http\Controllers\LogsController@getSupportLog');
                require base_path('app/OurEdu/Users/Admin/Routes/web.php');
                require base_path('app/OurEdu/Schools/Admin/Routes/web.php');
                require base_path('app/OurEdu/GradeClasses/Admin/Routes/routes.php');
                require base_path('app/OurEdu/Reports/Admin/Routes/routes.php');
                require base_path('app/OurEdu/PaymentReport/Admin/Routes/web.php');
                require base_path('app/OurEdu/QuestionReport/ContentAuthor/Routes/web.php');
                require base_path('app/OurEdu/Options/Admin/Routes/web.php');
                require base_path('app/OurEdu/Subjects/Admin/Routes/routes.php');
                require base_path('app/OurEdu/Translator/Admin/Routes/web.php');
                require base_path('app/OurEdu/Feedbacks/Admin/Routes/web.php');
                require base_path('app/OurEdu/VCRSchedules/Admin/Routes/web.php');
                require base_path('app/OurEdu/Config/Routes/web.php');
                require base_path('app/OurEdu/AppVersions/Routes/web.php');
                require base_path('app/OurEdu/SubjectPackages/Admin/Routes/web.php');
                require base_path('app/OurEdu/Instructors/Admin/Routes/web.php');

                require base_path('app/OurEdu/SchoolAccounts/SchoolAccounts/Admin/Routes/web.php');
                require base_path('app/OurEdu/SchoolAccounts/SchoolAccountBranches/Admin/Routes/web.php');
                require base_path('app/OurEdu/SchoolAccounts/SchoolRequests/Admin/Routes/web.php');


                require base_path('app/OurEdu/Courses/Admin/Routes/web.php');
                require base_path('app/OurEdu/PsychologicalTests/Admin/Routes/web.php');
                require base_path('app/OurEdu/Exams/Admin/Routes/web.php');
                require base_path('app/OurEdu/Contact/Routes/web.php');
                require base_path('app/OurEdu/StaticPages/Routes/web.php');


                require base_path('app/OurEdu/VCRSessions/Admin/Routes/web.php');
                require base_path('app/OurEdu/TextChat/Admin/Routes/web.php');

                require base_path('app/OurEdu/Certificates/Admin/Routes/web.php');
                require base_path('app/OurEdu/GradeColors/Admin/routes/web.php');
                require base_path('app/OurEdu/Payments/Admin/routes/web.php');
            });

            Route::group(['prefix' => 'school-account-manager', 'as' => 'school-account-manager.'], function () {
                require base_path(
                    'app/OurEdu/SchoolAccounts/SchoolAccountBranches/SchoolAccountManager/Routes/web.php'
                );
                require base_path('app/OurEdu/SchoolAccounts/Reports/routes/web.php');

                require base_path('app/OurEdu/Roles/SchoolManger/Routes/web.php');
                require base_path('app/OurEdu/Quizzes/SchoolManager/routes/web.php');
                require base_path('app/OurEdu/GeneralQuizzes/SchoolManager/Routes/web.php');
                require base_path('app/OurEdu/GeneralQuizzesReports/SchoolManager/Routes/web.php');
            });

            Route::group(['prefix' => 'school-branch-supervisor', 'as' => 'school-branch-supervisor.'], function () {
                Route::get(
                    '/spa/{vue_capture?}',
                    '\App\OurEdu\SchoolAccounts\SPA\Controllers\SPAController@getVueSupervisor'
                )->name('getVue')->where('vue_capture', '^(?!storage|login|register).*$');
                require base_path('app/OurEdu/SchoolAccounts/ClassroomClassSessions/SchoolSupervisor/Routes/web.php');
                require base_path('app/OurEdu/SchoolAccounts/GradeClasses/SchoolBranchSupervisor/Routes/web.php');
                require base_path('app/OurEdu/SchoolAccounts/SubjectInstructors/SchoolBranchSupervisor/Routes/web.php');
                require base_path('app/OurEdu/SchoolAccounts/InstructorRates/Supervisor/routes/web.php');
                require base_path('app/OurEdu/SchoolAccounts/SchoolBranchSupervisor/routes/web.php');
                require base_path('app/OurEdu/SchoolAccounts/ClassroomClass/SchoolBranchSupervisor/routes/web.php');
                require base_path('app/OurEdu/Quizzes/Routes/web.php');
                require base_path('app/OurEdu/GeneralQuizzes/SchoolSupervisor/Routes/web.php');
                require base_path("app/OurEdu/Assessments/Assessor/Routes/web.php");
                require base_path("app/OurEdu/Assessments/Assessee/Routes/web.php");
                require base_path("app/OurEdu/Assessments/AssessmentResultViewer/routes/web.php");
                require base_path('app/OurEdu/GeneralQuizzesReports/SchoolSupervisor/Routes/web.php');
            });
        });

        Route::get('/home', '\App\OurEdu\HomePage\Controllers\HomePageController@home');
        require base_path('app/OurEdu/SchoolAdmin/Routes/web.php');
    });
});


Route::get(
    'get-certificate/{id}',
    '\App\OurEdu\Certificates\Instructor\Controllers\Api\ThankingCertificateController@getCertificate'
);
Route::get('/home', '\App\OurEdu\HomePage\Controllers\HomePageController@home');
Route::get('payment/frame', [PaymentsApiController::class, 'paymentFrame'])->name('payment.frame');
Route::any('payfort/tokenization', [PaymentsApiController::class, 'handleTokenResponse']);
Route::any('payment/response', [PaymentsApiController::class, 'handleResponse'])
    ->name('payment.response');
