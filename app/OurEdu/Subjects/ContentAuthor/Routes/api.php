<?php
Route::group(['prefix' => 'subjects', 'as' => 'subjects.'], function () {
    Route::get('/', '\App\OurEdu\Subjects\ContentAuthor\Controllers\Api\SubjectApiController@getIndex')->name('get.contentAuthor.index');

    Route::post('tasks/{id}/pull', '\App\OurEdu\Subjects\ContentAuthor\Controllers\Api\TaskApiController@pullTask')->name('pullTask');
    Route::post('tasks/{id}/release', '\App\OurEdu\Subjects\ContentAuthor\Controllers\Api\TaskApiController@releaseTask')->name('releaseTask');

    Route::get('tasks/{id}/done', '\App\OurEdu\Subjects\ContentAuthor\Controllers\Api\TaskApiController@markTaskAsDone')->name('markTaskAsDone');

    Route::put('resources/{resourceId}/fill', '\App\OurEdu\Subjects\ContentAuthor\Controllers\Api\TaskApiController@fillResource')->name('fillResource');

    Route::get('resources/{resourceId}/fill', '\App\OurEdu\Subjects\ContentAuthor\Controllers\Api\TaskApiController@getFillResource')->name('getFillResource');
});
