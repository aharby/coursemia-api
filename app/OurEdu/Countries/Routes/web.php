<?php

Route::group(['prefix'=>'admin','as'=>'admin.'],function (){
    include base_path('app/OurEdu/Countries/Admin/Routes/web.php');
});
