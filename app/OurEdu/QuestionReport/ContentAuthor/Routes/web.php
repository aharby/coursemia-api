<?php
Route::group(['prefix'=>'question-reports','as'=>'questionReports.'], function () {

    Route::get('/', '\App\OurEdu\QuestionReport\ContentAuthor\Controllers\QuestionReportControllers@getIndex')->name('get.index');

});
