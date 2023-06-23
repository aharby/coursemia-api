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
    Route::delete('/{id}', [CoursesAdminController::class, 'deleteCourseCategory']);
    Route::get('/{id}', [CoursesAdminController::class, 'showCategory']);
    Route::put('/{id}', [CoursesAdminController::class, 'updateCategory']);
    Route::post('/{id}', [CoursesAdminController::class, 'addCategory']);
});
Route::group(['prefix' => 'sub-categories', 'as' => 'sub-categories.'], function () {
    Route::get('/', [CoursesAdminController::class, 'getCourseSubCategories']);
});

Route::group(['prefix' => 'get-course-by-category-id', 'as' => 'get-course-by-category-id.'], function () {
    Route::get('/', [CoursesAdminController::class, 'getCourseByCategoryId']);
});

Route::group(['prefix' => 'course-categories', 'as' => 'course-categories.'], function () {
    Route::get('/{id}', [CoursesAdminController::class, 'getCourseCategoriesList']);
});

Route::group(['prefix' => 'all-categories', 'as' => 'all-categories.'], function () {
    Route::get('/', [CoursesAdminController::class, 'getAllCategories']);
});

Route::group(['prefix' => 'all-categories-no-pagination', 'as' => 'all-categories-no-pagination.'], function () {
    Route::get('/', [CoursesAdminController::class, 'getAllCategoriesNoPagination']);
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
Route::group(['prefix' => 'single-course-images', 'as' => 'single-course-images.'], function () {
    Route::post('/', [CoursesAdminController::class, 'storeSingleCourseImages']);
});
Route::group(['prefix' => 'delete-course-image', 'as' => 'delete-course-image.'], function () {
    Route::post('/', [CoursesAdminController::class, 'deleteCourseImage']);
});
Route::group(['prefix' => 'get-course-images', 'as' => 'get-course-images.'], function () {
    Route::get('/', [CoursesAdminController::class, 'getCourseImages']);
});

Route::group(['prefix' => 'questions-and-answers', 'as' => 'questions-and-answers.'], function () {
    Route::post('/', [CoursesAdminController::class, 'storeQuestionsAndAnswers']);
});

Route::group(['prefix' => 'host-course-requests', 'as' => 'host-course-requests.'], function () {
    Route::get('/', [HostCourseRequestAdminController::class, 'index']);
});
