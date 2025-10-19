<?php
use Illuminate\Support\Facades\Route;
use \App\Modules\Users\Admin\Controllers\UsersController;
use App\Modules\Users\Auth\Controllers\Api\AuthApiController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('logout', [AuthApiController::class, 'logout']);
});

Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => 'auth:admin'], function () {
    Route::post('/assign-course-to-user', [UsersController::class , 'assignCourseToUser']);
    Route::post('/delete-course-from-user', [UsersController::class , 'deleteCourseFromUser']);
    Route::get('/courses', [UsersController::class , 'getUserCourses']);
    Route::get('/', [UsersController::class, 'index']);
    Route::put('/{id}', [UsersController::class , 'update']);
    Route::get('/{id}', [UsersController::class , 'show']);
    Route::delete('/{id}', [UsersController::class , 'delete']);
    Route::post('/delete-device/{id}', [UsersController::class , 'deleteDevice']);
});
