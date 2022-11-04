  <?php


use App\Modules\Offers\Controllers\OffersAdminApiControllers;

Route::group(['prefix' => 'offers', 'as' => 'offers.'], function () {
    Route::get('/', [OffersAdminApiControllers::class, 'index']);
    Route::get('/{id}', [OffersAdminApiControllers::class, 'show']);
    Route::post('/', [OffersAdminApiControllers::class, 'store']);
    Route::put('/{id}', [OffersAdminApiControllers::class, 'update']);
    Route::delete('/{id}', [OffersAdminApiControllers::class, 'destroy']);
});
