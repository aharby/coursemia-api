<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Users\Admin\Controllers\AuthController;
use \App\Modules\Users\Admin\Controllers\UsersController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => 'auth:admin'], function () {
    Route::post('/assign-course-to-user', [UsersController::class , 'assignCourseToUser']);
    Route::post('/delete-course-from-user', [UsersController::class , 'deleteCourseFromUser']);
    Route::get('/courses', [UsersController::class , 'getUserCourses']);
    Route::get('/', [UsersController::class, 'index']);
    Route::put('/{id}', [UsersController::class , 'update']);
    Route::get('/{id}', [UsersController::class , 'show']);
    Route::post('/delete-device/{id}', [UsersController::class , 'deleteDevice']);
    Route::post('/delete-user/{id}', [UsersController::class , 'deleteUser']);
});
