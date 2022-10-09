<?php

namespace App\Modules\BaseApp\Providers;

use App\Modules\Users\UseCases\ActivateUserUserCase\ActivateUserUseCase;
use App\Modules\Users\UseCases\ActivateUserUserCase\ActivateUserUseCaseInterface;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCase;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCaseInterface;
use Illuminate\Support\ServiceProvider;

class UseCasesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ActivateUserUseCaseInterface::class,
            ActivateUserUseCase::class);
        $this->app->bind(
            LoginUseCaseInterface::class,
            LoginUseCase::class
        );
    }
}
