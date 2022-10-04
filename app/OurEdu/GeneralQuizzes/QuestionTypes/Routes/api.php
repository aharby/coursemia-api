<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "questionTypes", 'namespace' => '\App\OurEdu\GeneralQuizzes\QuestionTypes\Controllers' , 'as' => 'questionTypes.'], function () {
    Route::get("/{generalQuiz}", "QuestionTypesController@index")->name('index');
});
