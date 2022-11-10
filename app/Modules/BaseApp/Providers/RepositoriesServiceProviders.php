<?php

namespace App\Modules\BaseApp\Providers;

use App\Modules\Countries\Repository\CountryRepository;
use App\Modules\Countries\Repository\CountryRepositoryInterface;
use App\Modules\Courses\Repository\FlashCardRepository;
use App\Modules\Courses\Repository\FlashCardRepositoryInterface;
use App\Modules\Courses\Repository\HostCourseRequestRepository;
use App\Modules\Courses\Repository\HostCourseRequestRepositoryInterface;
use App\Modules\Courses\Repository\LectureRepository;
use App\Modules\Courses\Repository\LectureRepositoryInterface;
use App\Modules\Courses\Repository\NoteRepository;
use App\Modules\Courses\Repository\NoteRepositoryInterface;
use App\Modules\Courses\Repository\QuestionsRepository;
use App\Modules\Courses\Repository\QuestionsRepositoryInterface;
use App\Modules\Events\Repository\EventRepository;
use App\Modules\Events\Repository\EventRepositoryInterface;
use App\Modules\Offers\Repository\OffersRepository;
use App\Modules\Offers\Repository\OffersRepositoryInterface;
use App\Modules\Specialities\Repository\SpecialitiesRepository;
use App\Modules\Specialities\Repository\SpecialitiesRepositoryInterface;
use App\Modules\StaticPages\Repository\DistinguishedStudentsRepository;
use App\Modules\StaticPages\Repository\DistinguishedStudentsRepositoryInterface;
use App\Modules\Users\UseCases\ActivateUserUseCase\ActivateUserUseCase;
use App\Modules\Users\UseCases\ActivateUserUseCase\ActivateUserUseCaseInterface;
use App\Modules\Users\Repository\UserRepository;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCase;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCaseInterface;
use App\Modules\Users\UseCases\RegisterUseCase\RegisterUseCase;
use App\Modules\Users\UseCases\RegisterUseCase\RegisterUseCaseInterface;
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
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            FlashCardRepositoryInterface::class,
            FlashCardRepository::class
        );
        $this->app->bind(
            ActivateUserUseCaseInterface::class,
            ActivateUserUseCase::class
        );

        $this->app->bind(
            ContentAuthorRepositoryInterface::class,
            ContentAuthorRepository::class
        );

        $this->app->bind(
            RegisterUseCaseInterface::class,
            RegisterUseCase::class
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
            CountryRepositoryInterface::class,
            CountryRepository::class
        );
        $this->app->bind(
            SpecialitiesRepositoryInterface::class,
            SpecialitiesRepository::class
        );
        $this->app->bind(
            QuestionsRepositoryInterface::class,
            QuestionsRepository::class
        );

        $this->app->bind(
            EventRepositoryInterface::class,
            EventRepository::class
        );
        $this->app->bind(
            LectureRepositoryInterface::class,
            LectureRepository::class
        );
        $this->app->bind(
            NoteRepositoryInterface::class,
            NoteRepository::class
        );
        $this->app->bind(
            OffersRepositoryInterface::class,
            OffersRepository::class
        );
        $this->app->bind(
            HostCourseRequestRepositoryInterface::class,
            HostCourseRequestRepository::class
        );
    }
}
