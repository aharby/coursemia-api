<?php
Route::group(['prefix' => 'reports', 'as' => 'report.'], function () {

    Route::post('/{subjectId}/{reportType}/{id}', '\App\OurEdu\Reports\Student\Controllers\Api\ReportController@postCreateReport')->name('post.create');

});
