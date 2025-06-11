<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ContactUs\Controllers\ContactUsController;



Route::post('/contact-us', [ContactUsController::class, 'submitContactUs']);
Route::post('/feedback', [ContactUsController::class, 'submitFeedback']);