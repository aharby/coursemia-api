<?php


namespace App\OurEdu\Users\UseCases\SuspendUserUseCase;



use App\OurEdu\Users\Repository\UserRepositoryInterface;

Interface  SuspendUserUseCaseInterface
{

    //Should Work like toggle of suspend user if not suspended and remove suspend if suspended
    public function suspendUser(UserRepositoryInterface $userRepository , int $id) :bool ;
}
