<?php


use App\Modules\Events\Controllers\EventsAdminApiControllers;

Route::group(['prefix' => 'events', 'as' => 'events.'], function () {
    Route::get('/', [EventsAdminApiControllers::class, 'index']);
    Route::get('/{id}', [EventsAdminApiControllers::class, 'show']);
    Route::post('/', [EventsAdminApiControllers::class, 'store']);
    Route::put('/{id}', [EventsAdminApiControllers::class, 'update']);
    Route::delete('/{id}', [EventsAdminApiControllers::class, 'destroy']);
});
