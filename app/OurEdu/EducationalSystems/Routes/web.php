<?php
Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
    include base_path('app/OurEdu/EducationalSystems/Admin/Routes/web.php');
});
