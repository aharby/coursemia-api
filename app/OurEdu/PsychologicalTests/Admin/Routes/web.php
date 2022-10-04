<?php
Route::group(['prefix'=>'psychological-tests','as'=>'psychological_tests.'], function () {
    Route::get('/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalTestController@getIndex')->name('get.index');

    Route::get('create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalTestController@getCreate')->name('get.create');

    Route::post('create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalTestController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalTestController@getEdit')->name('get.edit');

    Route::put('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalTestController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalTestController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalTestController@delete')->name('delete');
});

Route::group(['prefix'=>'psychological-questions','as'=>'psychological_questions.'], function () {
    Route::get('/{testId}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalQuestionController@getIndex')->name('get.index');

    Route::get('{testId}/create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalQuestionController@getCreate')->name('get.create');

    Route::post('{testId}/create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalQuestionController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalQuestionController@getEdit')->name('get.edit');

    Route::put('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalQuestionController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalQuestionController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalQuestionController@delete')->name('delete');
});


Route::group(['prefix'=>'psychological-options','as'=>'psychological_options.'], function () {
    Route::get('/{testId}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalOptionController@getIndex')->name('get.index');

    Route::get('{testId}/create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalOptionController@getCreate')->name('get.create');

    Route::post('{testId}/create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalOptionController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalOptionController@getEdit')->name('get.edit');

    Route::put('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalOptionController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalOptionController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalOptionController@delete')->name('delete');
});


Route::group(['prefix'=>'psychological-recomendations','as'=>'psychological_recomendations.'], function () {
    Route::get('/{testId}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalRecomendationController@getIndex')->name('get.index');

    Route::get('{testId}/create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalRecomendationController@getCreate')->name('get.create');

    Route::post('{testId}/create/', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalRecomendationController@postCreate')->name('post.create');

    Route::get('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalRecomendationController@getEdit')->name('get.edit');

    Route::put('edit/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalRecomendationController@putEdit')->name('put.edit');

    Route::get('view/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalRecomendationController@getView')->name('get.view');

    Route::delete('delete/{id}', '\App\OurEdu\PsychologicalTests\Admin\Controllers\PsychologicalRecomendationController@delete')->name('delete');
});
