<?php
Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
    include base_path('app/OurEdu/AcademicYears/Admin/Routes/web.php');
});
