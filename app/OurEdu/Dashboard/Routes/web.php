<?php
Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
    require base_path('app/OurEdu/Dashboard/Admin/Routes/web.php');
});

