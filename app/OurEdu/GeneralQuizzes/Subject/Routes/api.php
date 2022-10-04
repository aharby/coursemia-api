<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "subject", 'namespace' => '\App\OurEdu\GeneralQuizzes\Subject\Controllers' , 'as' => 'subject.'], function () {
    Route::get("{subject}/sections", "SubjectSectionsController@SubjectSections");

    Route::get("/subsections/{section}", "SubjectSectionsController@subsections");
});
