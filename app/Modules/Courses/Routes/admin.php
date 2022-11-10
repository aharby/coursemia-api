<?php

use App\Modules\Courses\Controllers\Admin\CourseFlashCardAdminController;
use Illuminate\Support\Facades\Route;
use \App\Modules\Courses\Controllers\Admin\HostCourseRequestAdminController;
use App\Modules\Courses\Controllers\Admin\CoursesAdminController;
use \App\Modules\Courses\Controllers\Admin\LecturesAdminController;
use \App\Modules\Courses\Controllers\Admin\QuestionsAdminController;
use \App\Modules\Courses\Controllers\Admin\NotesAdminController;

Route::group(['prefix' => 'courses', 'as' => 'courses.'], function () {
    Route::get('/', [CoursesAdminController::class, 'index']);
    Route::post('/', [CoursesAdminController::class, 'store']);
    Route::post('/store-categories', [CoursesAdminController::class, 'storeCourseCategories']);
    Route::get('/{id}', [CoursesAdminController::class, 'show']);
    Route::put('/{id}', [CoursesAdminController::class, 'update']);
    Route::delete('/{id}', [CoursesAdminController::class, 'destroy']);
});

Route::group(['prefix' => 'course-reviews', 'as' => 'course-reviews.'], function () {
    Route::get('/{id}', [CoursesAdminController::class, 'getCourseReviews']);
    Route::delete('/{id}', [CoursesAdminController::class, 'deleteCourseReview']);
});

Route::group(['prefix' => 'all-courses', 'as' => 'all-courses.'], function () {
    Route::get('/', [CoursesAdminController::class, 'allCourses']);
});
Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
    Route::get('/', [CoursesAdminController::class, 'getCourseCategories']);
});

Route::group(['prefix' => 'flashcards', 'as' => 'flashcards.'], function () {
    Route::post('/', [CoursesAdminController::class, 'storeCourseFlashCards']);
});

Route::group(['prefix' => 'flash-cards', 'as' => 'flash-cards.'], function () {
    Route::get('/', [CourseFlashCardAdminController::class, 'index']);
    Route::post('/', [CourseFlashCardAdminController::class, 'store']);
    Route::put('/{id}', [CourseFlashCardAdminController::class, 'update']);
    Route::get('/{id}', [CourseFlashCardAdminController::class, 'show']);
    Route::delete('/{id}', [CourseFlashCardAdminController::class, 'destroy']);
});

Route::group(['prefix' => 'questions', 'as' => 'questions.'], function () {
    Route::get('/', [QuestionsAdminController::class, 'index']);
    Route::put('/{id}', [QuestionsAdminController::class, 'update']);
    Route::get('/{id}', [QuestionsAdminController::class, 'show']);
    Route::delete('/{id}', [QuestionsAdminController::class, 'destroy']);
    Route::post('/', [QuestionsAdminController::class, 'store']);
    Route::post('/upload-pdf', [QuestionsAdminController::class, 'uploadPdf']);
});

Route::group(['prefix' => 'lectures', 'as' => 'lectures.'], function () {
    Route::get('/', [LecturesAdminController::class, 'index']);
    Route::get('/{id}', [LecturesAdminController::class, 'show']);
    Route::put('/{id}', [LecturesAdminController::class, 'update']);
    Route::delete('/{id}', [LecturesAdminController::class, 'delete']);
    Route::post('/', [CoursesAdminController::class, 'storeCourseLectures']);
    Route::post('/upload-video', [CoursesAdminController::class, 'uploadToVimeo']);
});

Route::group(['prefix' => 'course-lectures', 'as' => 'course-lectures.'], function () {
    Route::post('/', [LecturesAdminController::class, 'store']);
});
Route::group(['prefix' => 'course-notes', 'as' => 'course-notes.'], function () {
    Route::post('/', [CoursesAdminController::class, 'storeCourseNotes']);
});

Route::group(['prefix' => 'notes', 'as' => 'notes.'], function () {
    Route::get('/', [NotesAdminController::class, 'index']);
    Route::put('/{id}', [NotesAdminController::class, 'update']);
    Route::get('/{id}', [NotesAdminController::class, 'show']);
    Route::delete('/{id}', [NotesAdminController::class, 'destroy']);
    Route::post('/', [NotesAdminController::class, 'store']);
    Route::post('/upload-pdf', [CoursesAdminController::class, 'uploadPdf']);
});

Route::group(['prefix' => 'course-images', 'as' => 'course-images.'], function () {
    Route::post('/', [CoursesAdminController::class, 'storeCourseImages']);
});

Route::group(['prefix' => 'questions-and-answers', 'as' => 'questions-and-answers.'], function () {
    Route::post('/', [CoursesAdminController::class, 'storeQuestionsAndAnswers']);
});

Route::group(['prefix' => 'host-course-requests', 'as' => 'host-course-requests.'], function () {
    Route::get('/', [HostCourseRequestAdminController::class, 'index']);
});
