<?php

namespace App\Modules\BaseApp\Providers;

use App\Modules\StaticPages\Repository\DistinguishedStudentsRepository;
use App\Modules\StaticPages\Repository\DistinguishedStudentsRepositoryInterface;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCase;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCaseInterface;
use App\Modules\Users\UseCases\SendLoginOtp\SendLoginOtp;
use App\Modules\Users\UseCases\SendLoginOtp\SendLoginOtpImp;
use Illuminate\Support\ServiceProvider;
use App\Modules\Config\Repository\ConfigRepository;
use App\Modules\Users\Repository\StudentRepository;
use App\Modules\Users\Repository\UserLogsRepository;
use App\Modules\Users\Repository\InstructorRepository;
use App\Modules\Users\Repository\ContentAuthorRepository;
use App\Modules\Users\Repository\StudentTeacherRepository;
use App\Modules\Config\Repository\ConfigRepositoryInterface;
use App\Modules\Users\Repository\StudentRepositoryInterface;
use App\Modules\StaticPages\Repository\StaticPagesRepository;
use App\Modules\Users\Repository\UserLogsRepositoryInterface;
use App\Modules\Users\Repository\InstructorRepositoryInterface;
use App\Modules\Users\Repository\ContentAuthorRepositoryInterface;
use App\Modules\Users\Repository\StudentTeacherRepositoryInterface;
use App\Modules\StaticPages\Repository\StaticPagesRepositoryInterface;

class RepositoriesServiceProviders extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'App\Modules\Users\Repository\UserRepositoryInterface',
            'App\Modules\Users\Repository\UserRepository'
        );
        $this->app->bind(
            ContentAuthorRepositoryInterface::class,
            ContentAuthorRepository::class
        );

        $this->app->bind(
            SendLoginOtp::class,
            SendLoginOtpImp::class
        );

        $this->app->bind(
            LoginUseCaseInterface::class,
            LoginUseCase::class
        );

        $this->app->bind(
            InstructorRepositoryInterface::class,
            InstructorRepository::class
        );

        $this->app->bind(
            StudentRepositoryInterface::class,
            StudentRepository::class
        );
        $this->app->bind(
            ConfigRepositoryInterface::class,
            ConfigRepository::class
        );

        $this->app->bind(
            StaticPagesRepositoryInterface::class,
            StaticPagesRepository::class
        );

        $this->app->bind(
            UserLogsRepositoryInterface::class,
            UserLogsRepository::class
        );

        $this->app->bind(
            StudentTeacherRepositoryInterface::class,
            StudentTeacherRepository::class
        );
        $this->app->bind(
            DistinguishedStudentsRepositoryInterface::class,
            DistinguishedStudentsRepository::class
        );
}
}
