<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'certificate/thanking',
    'as' => 'certificates.thanking.',
    'namespace' => '\App\OurEdu\Certificates\Admin\Controllers',
], function () {
    Route::get('/', 'ThankingCertificateController@index')->name('index');
    Route::get('/create', 'ThankingCertificateController@create')->name('create');
    Route::post('/store', 'ThankingCertificateController@store')->name('store');
    Route::get('/{certificate}/edit', 'ThankingCertificateController@edit')->name('edit');
    Route::put('/{certificate}/edit', 'ThankingCertificateController@update')->name('update');
    Route::delete('/{certificate}', 'ThankingCertificateController@destroy')->name('destroy');
    Route::get('/demo/{certificate}', 'ThankingCertificateController@PrintDemoCertificate')->name('demo');
});
