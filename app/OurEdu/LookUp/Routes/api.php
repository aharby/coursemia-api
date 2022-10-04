<?php
Route::group(['prefix'=>'look-up','as'=>'lookUp.'], function () {

    Route::get('/', '\App\OurEdu\LookUp\Controllers\Api\LookUpController@getIndex')->name('get.index');

});
