<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
    'prefix'=>"formative-test",
    "namespace" => "\App\OurEdu\SchoolAdmin\FormativeTest\Controllers",
    'as'=>"formative-test.",
    ],
    function () {
        Route::get("create", "FormativeTestController@create")->name("create");
        Route::post("store", "FormativeTestController@store")->name("store");
        Route::get("index", "FormativeTestController@index")->name("index");
        Route::get("/edit/{formativeTest}", "FormativeTestController@edit")->name("edit");
        Route::post('edit/{formativeTest}', 'FormativeTestController@update')->name('update');
        Route::DELETE("formative-test/delete/{formativeTest}", "FormativeTestController@delete")->name("delete");
        Route::get("publish/{formativeTest}", "FormativeTestController@publish")->name("publish");

        Route::group(
            [
            'prefix'=>"ajax",
            'as'=>"ajax.",
            ],
            function () {
                Route::get('get-grade-class-by-educational-system', 'AjaxController@getEducationalSystemGradeClasses')->name('get.educational.system.grade.classes');
                Route::get('get-subjects-by-grade-classes', 'AjaxController@getSubjectsByGrades')->name('get.grade.classes.subjects');
                Route::get('get-subject-sections/{subject?}', 'AjaxController@getSubjectMainSections')->name('get.subject.sections');
            }
        );

        Route::get("questions/{generalQuiz}", "FormativeTestQuestionsController@index")->name("questions");
        Route::DELETE(
            "questions/delete/{generalQuiz}/{generalQuizQuestionBank}",
            "FormativeTestQuestionsController@delete"
        )->name("questions.delete");
        Route::POST("clone/{formativeTest}", "FormativeTestController@clone")->name("clone");
        Route::get("clone/{formativeTest}", "FormativeTestController@getClone")->name("get.clone");
    }
);
