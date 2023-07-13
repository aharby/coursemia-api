<?php
use Illuminate\Support\Facades\Route;
use \App\Modules\Settings\Controllers\SettingsApiController;

Route::get('/about-us', [SettingsApiController::class , 'aboutUs']);
Route::get('/privacy-policy', [SettingsApiController::class , 'privacyPolicy']);
Route::get('/terms-and-conditions', [SettingsApiController::class , 'termsAndConditions']);

