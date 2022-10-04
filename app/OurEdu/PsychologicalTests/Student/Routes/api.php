<?php

Route::group(['prefix'=>'psychological-tests','as'=>'psychological_tests.'], function () {
    Route::get('/', '\App\OurEdu\PsychologicalTests\Student\Controllers\PsychologicalTestApiController@getIndex')->name('get.index');
    Route::get('/{id}', '\App\OurEdu\PsychologicalTests\Student\Controllers\PsychologicalTestApiController@getView')->name('get.view.psychological_test');

    Route::post('start/{id}', '\App\OurEdu\PsychologicalTests\Student\Controllers\PsychologicalTestApiController@getStart')->name('post.start');

    Route::get('start/{id}', '\App\OurEdu\PsychologicalTests\Student\Controllers\PsychologicalTestApiController@getStart')->name('get.start');

    Route::post('finish/{id}', '\App\OurEdu\PsychologicalTests\Student\Controllers\PsychologicalTestApiController@getFinish')->name('post.finish');

    Route::get('/{id}/questions', '\App\OurEdu\PsychologicalTests\Student\Controllers\PsychologicalTestApiController@getNotAnsweredQuestions')->name('get.questions');

    Route::post('/{id}/questions', '\App\OurEdu\PsychologicalTests\Student\Controllers\PsychologicalTestApiController@answerQuestion')->name('post.answerQuestion');
});
