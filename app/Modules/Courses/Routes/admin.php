<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Courses\Controllers\Admin\CoursesAdminController;

Route::group(['prefix' => 'courses', 'as' => 'courses.'], function () {
    Route::get('/', [CoursesAdminController::class, 'index']);
    Route::post('/', [CoursesAdminController::class, 'store']);
    Route::get('/{id}', [CoursesAdminController::class , 'show']);
    Route::put('/{id}', [CoursesAdminController::class , 'update']);
    Route::delete('/{id}', [CoursesAdminController::class , 'destroy']);
});
