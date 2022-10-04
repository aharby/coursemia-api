<?php

use App\OurEdu\Courses\Discussion\Controllers\DiscussionCommentsController;
use Illuminate\Support\Facades\Route;
use App\OurEdu\Courses\Discussion\Controllers\CourseDiscussionController;

Route::group(
    [
        'prefix' => 'discussions',
        'as' => 'discussions.'
    ],
    function () {
        Route::get('{course}', [CourseDiscussionController::class, 'index'])->name("index");
        Route::post('{course}', [CourseDiscussionController::class, 'store'])->name("store");
        Route::get('{courseDiscussion}/show', [CourseDiscussionController::class, 'show'])->name("show");
        Route::put('{courseDiscussion}/update-discussion', [CourseDiscussionController::class, 'update'])->name('update');
        Route::delete("{courseDiscussion}/delete-discussion", [CourseDiscussionController::class, 'delete'])->name("delete");
        Route::get("/{courseDiscussion}/comments", [DiscussionCommentsController::class, 'index'])->name("comments");
        Route::post("/{courseDiscussion}/comments", [DiscussionCommentsController::class, 'store'])->name("comments.store");
        Route::put("{courseDiscussionComment}/update-comment", [DiscussionCommentsController::class, 'update'])->name("comments.update");
        Route::delete("{courseDiscussionComment}/delete-comment", [DiscussionCommentsController::class, 'delete'])->name("comments.delete");

        require base_path('app/OurEdu/Courses/Discussion/Instructor/Routes/api.php');
    }
);
