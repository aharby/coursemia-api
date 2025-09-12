<?php


Route::group(['as' => 'api.', 'middleware' => ['checkDeviceAndToken', 'userSuspended']], function () {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        require base_path('app/Modules/Users/Auth/Routes/api.php');
    });

    require base_path('app/Modules/HomeScreen/Routes/api.php');
    require base_path('app/Modules/Courses/Routes/api.php');
    require base_path('app/Modules/WantToLearn/Lectures/Routes/api.php');
    require base_path('app/Modules/WantToLearn/Flashcards/Routes/api.php');
    require base_path('app/Modules/WantToLearn/Questions/Routes/api.php');
    require base_path('app/Modules/WantToLearn/api.php');
    require base_path('app/Modules/Settings/Routes/api.php');
    require base_path('app/Modules/MyProgress/Routes/api.php');
    require base_path('app/Modules/Countries/Routes/api.php');
    require base_path('app/Modules/Config/Routes/api.php');
    require base_path('app/Modules/Specialities/Routes/api.php');
    require base_path('app/Modules/GarbageMedia/Routes/api.php');
    require base_path('app/Modules/Post/Routes/api.php');
});

Route::group([], function () {
    require base_path('app/Modules/Payment/Routes/api.php');
    require base_path('app/Modules/ContactUs/Routes/api.php');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function (){
    require base_path('app/Modules/Users/Admin/Routes/admin.php');
    Route::post('upload-image', [\App\Modules\Config\Controllers\ConfigsController::class, 'uploadImage']);
});
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth:api', 'role:admin']], function () {
    require base_path('app/Modules/GarbageMedia/Routes/api.php');
    require base_path('app/Modules/Config/Routes/admin.php');
    require base_path('app/Modules/Settings/Routes/admin.php');
    require base_path('app/Modules/Countries/Routes/admin.php');
    require base_path('app/Modules/Specialities/Routes/admin.php');
    require base_path('app/Modules/Courses/Routes/admin.php');
    require base_path('app/Modules/Events/Routes/admin.php');
    require base_path('app/Modules/Offers/Routes/admin.php');
});

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function(){
    require base_path('app/Modules/Users/Auth/Routes/noAuth.php');    
});

// these routes are for testing purposes only
Route::group([], function () {
    Route::post('delete-user/{id}',[\App\Modules\Users\Admin\Controllers\UsersController::class, 'deleteUser']);
    });

Route::get('/test-question-of-the-day', function () {
    Illuminate\Support\Facades\Artisan::call('update:question-of-the-day');
    return response()->json([
        'status' => 'success',
        'message' => 'Command executed successfully',
    ]);
});

