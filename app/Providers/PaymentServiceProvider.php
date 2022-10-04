<?php

namespace App\Providers;

use App\OurEdu\Payments\Gateways\PayfortGateway;
use App\OurEdu\Payments\Gateways\UrWayGateway;
use App\OurEdu\Payments\Gateways\PaymentGatewayRegistry;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentGatewayRegistry::class);
    }

    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->app->make(PaymentGatewayRegistry::class)
            ->register("payfort", $this->app->make(PayfortGateway::class))
            ->register("urway", $this->app->make(UrWayGateway::class));
    }
}
