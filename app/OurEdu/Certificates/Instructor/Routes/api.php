<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'certificate/thanking',
    'as' => 'certificates.thanking.',
    'namespace' => '\App\OurEdu\Certificates\Instructor\Controllers\Api',
], function () {
    Route::get('/', 'ThankingCertificateController@index')->name('index');
    Route::post('/certificate/student', 'ThankingCertificateController@CertificateStudent')->name('certificate.student');
});
