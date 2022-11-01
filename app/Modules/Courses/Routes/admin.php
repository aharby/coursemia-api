<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Courses\Controllers\Admin\CoursesAdminController;
use \App\Modules\Courses\Controllers\Admin\LecturesAdminController;

Route::group(['prefix' => 'courses', 'as' => 'courses.'], function () {
    Route::get('/', [CoursesAdminController::class, 'index']);
    Route::post('/', [CoursesAdminController::class, 'store']);
    Route::post('/store-categories', [CoursesAdminController::class, 'storeCourseCategories']);
    Route::get('/{id}', [CoursesAdminController::class , 'show']);
    Route::put('/{id}', [CoursesAdminController::class , 'update']);
    Route::delete('/{id}', [CoursesAdminController::class , 'destroy']);
});

Route::group(['prefix' => 'categories', 'as' => 'categories.'], function (){
    Route::get('/', [CoursesAdminController::class, 'getCourseCategories']);
});

Route::group(['prefix' => 'flashcards', 'as' => 'flashcards.'], function (){
    Route::post('/', [CoursesAdminController::class, 'storeCourseFlashCards']);
});

Route::group(['prefix' => 'notes', 'as' => 'notes.'], function (){
    Route::post('/', [CoursesAdminController::class, 'storeCourseNotes']);
    Route::post('/upload-pdf', [CoursesAdminController::class, 'uploadPdf']);
});

Route::group(['prefix' => 'lectures', 'as' => 'lectures.'], function (){
    Route::get('/', [LecturesAdminController::class, 'index']);
    Route::post('/', [CoursesAdminController::class, 'storeCourseLectures']);
    Route::post('/upload-video', [CoursesAdminController::class, 'uploadToVimeo']);
});

Route::group(['prefix' => 'course-images', 'as' => 'course-images.'], function (){
    Route::post('/', [CoursesAdminController::class, 'storeCourseImages']);
});

    Route::group(['prefix' => 'questions-and-answers', 'as' => 'questions-and-answers.'], function (){
    Route::post('/', [CoursesAdminController::class, 'storeQuestionsAndAnswers']);
});
