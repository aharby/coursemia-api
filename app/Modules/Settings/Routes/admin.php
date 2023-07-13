<?php
use Illuminate\Support\Facades\Route;
use \App\Modules\Settings\Controllers\SettingsAdminController;

Route::get('/about-us', [SettingsAdminController::class , 'getAboutUs']);
Route::put('/about-us', [SettingsAdminController::class , 'postAboutUs']);
Route::get('/privacy-policy', [SettingsAdminController::class , 'getPrivacyPolicy']);
Route::put('/privacy-policy', [SettingsAdminController::class , 'postPrivacyPolicy']);
Route::get('/terms-and-conditions', [SettingsAdminController::class , 'getTermsAndConditions']);
Route::put('/terms-and-conditions', [SettingsAdminController::class , 'postTermsAndConditions']);

