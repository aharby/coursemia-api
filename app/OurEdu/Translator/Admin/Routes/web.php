<?php

Route::group(['prefix' => 'translator' , 'as'=>'translator.'], function () {
Route::get('/', '\App\OurEdu\Translator\Admin\Controllers\TranslatorController@getIndex')->name('get.index');

Route::get('/edit/{id}', '\App\OurEdu\Translator\Admin\Controllers\TranslatorController@getEdit')->name('get.edit');
Route::put('/edit/{id}', '\App\OurEdu\Translator\Admin\Controllers\TranslatorController@postEdit')->name('put.edit');

});
