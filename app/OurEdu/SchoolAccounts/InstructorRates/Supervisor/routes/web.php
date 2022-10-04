<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'instructors-rates',
    'as' => 'instructors-rates.',
    'namespace'=>'\App\OurEdu\SchoolAccounts\InstructorRates\Supervisor\Controllers'
],function (){
    Route::get('/','InstructorRatesController@getIndex')->name('get.index');
    Route::get('/export','InstructorRatesController@exportRatesOfInstructor')->name('export.all');
    Route::get('/view/{instructor_id}','InstructorRatesController@getView')->name('get.view');
    Route::get('/export/{instructor}','InstructorRatesController@ExportInstructorRates')->name('get.export');
});
