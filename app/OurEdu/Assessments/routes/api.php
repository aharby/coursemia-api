<?php

use Illuminate\Support\Facades\Route;
Route::group(
    ['prefix' => 'assessments', 'as' => 'assessments.', 'middleware' => ['auth:api']],
    function () {
        require base_path('app/OurEdu/Assessments/AssessmentManager/routes/api.php');
        Route::get("look-up", "\App\OurEdu\Assessments\Lookup\Controllers\Api\LookUpController@index");
        require base_path('app/OurEdu/Assessments/Assessor/Routes/api.php');
        require base_path('app/OurEdu/Assessments/AssessmentResultViewer/routes/api.php');
        require base_path('app/OurEdu/Assessments/Assessee/Routes/api.php');
        Route::get("index", "\App\OurEdu\Assessments\Lookup\Controllers\Api\LookUpController@getAssesments");
    }
);
