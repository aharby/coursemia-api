<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "prefix"=>"instructor",
        "as" => "instructor.",
        "namespace"=>"\App\OurEdu\Courses\Discussion\Instructor\Controllers"
    ],
    function () {
        Route::get("{course}/students/list", "CourseDiscussionController@listStudents")->name("listStudents");
        Route::get("{course}/{student}/toggle/active", "CourseDiscussionController@toggleStudentActivation")->name("toggleStudentActivation");
        Route::delete('{courseDiscussion}/delete-discussion', "CourseDiscussionController@deleteDiscussion")->name("deleteDiscussion");
        Route::delete('{courseDiscussionComment}/delete-comment', "CourseDiscussionController@deleteDiscussionComment")->name("deleteDiscussionComment");
    }
);
