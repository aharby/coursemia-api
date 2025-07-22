<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('/health', function () {
        echo 'Coursemia API is up and running!';
});

Route::get('/pages/privacy-policy', function () {
    return view('privacy-policy', [
        'title' => 'Coursemia Privacy Policy',
        'content' => \Illuminate\Support\Facades\DB::table('settings')
                        ->where('key', 'privacy_policy')
                        ->value('value')
    ]);
});