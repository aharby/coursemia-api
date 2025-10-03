<?php

use App\Enums\StatusCodesEnum;

use Illuminate\Support\Facades\Route;

Route::any('/no-auth', function(){
    return customResponse(null,"Authentication required", 401, StatusCodesEnum::UNAUTHORIZED);
});