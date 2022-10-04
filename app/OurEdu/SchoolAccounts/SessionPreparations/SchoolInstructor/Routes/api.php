<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'session-preparation'
    ,'as' => 'sessionPreparation.'
    ,'namespace'=>'\App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Controllers'
],function(){
    Route::get('/{sessionId}','SessionPreparationsApiController@getClassSessionPreparation');
    Route::post('/{sessionId}','SessionPreparationsApiController@save');
    Route::get('/subject/{subject}/sections','SubjectSectionsController@SubjectSections')->name('view.subject.sections');
    Route::get('/subject/subsections/{section}','SubjectSectionsController@subsections')->name('view.sections.subsections');
    Route::get('/{sessionId}/lookups/','SessionPreparationsApiController@getClassSessionPreparationLookUps');
});

Route::group([
    'prefix'=>'media-library',
    'as' => 'mediaLibrary.',
    'namespace' =>  '\App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Controllers'
], function () {
    Route::get("/", "SessionPreparationsApiController@mediaLibrary");
});

Route::group([
    'prefix'=>'school-media-library',
    'as' => 'mediaLibrary.',
    'namespace' =>  '\App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Controllers'
], function () {
    Route::get("/", "SessionPreparationsApiController@schoolMediaLibrary");
});
