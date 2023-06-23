<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('country', function(){
//    $countries = countries();
//    $countries = json_decode($countries);
////    \App\Modules\Countries\Models\Country::orderBy('id', 'DESC')->delete();
//    foreach ($countries as $country){
//        $countryModel = new \App\Modules\Countries\Models\Country;
//        $root = $country->idd->root ?? 0;
//        $suffix = $country->idd->suffixes[0] ?? 0;
//        $countryModel->{'title:en'} = $country->name->common;
//        $countryModel->{'title:ar'} = $country->translations->ara->common;
//        $countryModel->country_code = $root.''.$suffix;
//        $countryModel->flag = $country->flags->png;
//        $countryModel->is_active = 1;
//        $countryModel->save();
//    }
//});
Route::group(['as' => 'api.', 'middleware' => ['checkDeviceAndToken', 'userSuspended']], function () {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        require base_path('app/Modules/Users/Auth/Routes/api.php');
    });

    require base_path('app/Modules/HomeScreen/Routes/api.php');
    require base_path('app/Modules/Courses/Routes/api.php');
    require base_path('app/Modules/Countries/Routes/api.php');
    require base_path('app/Modules/Config/Routes/api.php');
    require base_path('app/Modules/Specialities/Routes/api.php');
    require base_path('app/Modules/GarbageMedia/Routes/api.php');
    require base_path('app/Modules/Post/Routes/api.php');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function (){
    require base_path('app/Modules/Users/Admin/Routes/admin.php');
});
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    require base_path('app/Modules/GarbageMedia/Routes/api.php');
    require base_path('app/Modules/Config/Routes/admin.php');
    require base_path('app/Modules/Countries/Routes/admin.php');
    require base_path('app/Modules/Specialities/Routes/admin.php');
    require base_path('app/Modules/Courses/Routes/admin.php');
    require base_path('app/Modules/Events/Routes/admin.php');
    require base_path('app/Modules/Offers/Routes/admin.php');
});
