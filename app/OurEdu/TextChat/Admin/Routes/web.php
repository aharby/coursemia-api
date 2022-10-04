<?php
Route::group(['prefix'=>'text-chat' , 'as' => 'textChat.'], function () {
    Route::get('/', '\App\OurEdu\TextChat\Admin\Controllers\TextChatController@index')->name('index');
    Route::get('/chat-room/{name}/{room}', '\App\OurEdu\TextChat\Admin\Controllers\TextChatController@chatRoom')->name('chatRoom');
//    Route::get('/list-rooms', '\App\OurEdu\HomePage\Controllers\ChatTestController@listRooms')->name('listRooms');
    Route::post('/create-room', '\App\OurEdu\TextChat\Admin\Controllers\TextChatController@createRoom')->name('createRoom');
    Route::post('/join-room/', '\App\OurEdu\TextChat\Admin\Controllers\TextChatController@joinRoom')->name('joinRoom');
    Route::post('/add-user/', '\App\OurEdu\TextChat\Admin\Controllers\TextChatController@addUserToRoom')->name('addUserToRoom');
    Route::get('/room-messages/{room}', '\App\OurEdu\TextChat\Admin\Controllers\TextChatController@roomMessages')->name('roomMessages');
});
