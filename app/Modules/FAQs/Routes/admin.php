<?php
use Illuminate\Support\Facades\Route;

use App\Modules\FAQs\Controllers\FAQAdminApiController;

Route::group(['as' => 'faq.'], function () {
    Route::resource('faqs', FAQAdminApiController::class);
});
