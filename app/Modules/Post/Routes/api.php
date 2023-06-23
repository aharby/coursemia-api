<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Post\Controllers\PostsApiControllers;
use \App\Modules\Post\Controllers\FollowApiControllers;

Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
    Route::get('/', [PostsApiControllers::class , 'getPosts']);
    Route::post('/', [PostsApiControllers::class , 'store']);
    Route::get('/{id}', [PostsApiControllers::class , 'getPost']);
    Route::post('add-comment', [PostsApiControllers::class, 'addComment']);
    Route::post('add-remove-like', [PostsApiControllers::class, 'addLike']);
});

Route::group(['prefix' => 'post-writer', 'as' => 'post-writer.'], function () {
    Route::get('/', [PostsApiControllers::class , 'getPostOwner']);
});

Route::group(['prefix' => 'my-posts', 'as' => 'my-posts.'], function () {
    Route::get('/', [PostsApiControllers::class , 'getMyPosts']);
});

Route::group(['prefix' => 'follow-un-follow', 'as' => 'follow-un-follow.'], function () {
    Route::post('/', [FollowApiControllers::class , 'followUnFollowUser']);
    Route::get('/get-follow-requests', [FollowApiControllers::class , 'getFollowRequestsList']);
    Route::get('/my-followers', [FollowApiControllers::class , 'getMyFollowers']);
    Route::post('/accept-reject-follow-request', [FollowApiControllers::class , 'acceptRejectFollowRequest']);
});

