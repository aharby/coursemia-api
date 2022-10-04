<?php

declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\UpdateProfileUseCase;


use App\OurEdu\Users\Repository\UserRepositoryInterface;

interface UpdateProfileUseCaseInterface
{
    /**
     * @param array $data
     * @param UserRepositoryInterface $userRepository
     * @param bool $garbageMedia
     * @param bool $apiAuth
     * @return array
     */
    public function updateProfile(array $data, UserRepositoryInterface $userRepository): array;

    /**
     * @param array $data
     * @param UserRepositoryInterface $userRepository
     * @return array
     */
    public function updatePassword(array $data, UserRepositoryInterface $userRepository): array;


    public function updateLanguage(array $data, UserRepositoryInterface $userRepository): array;
}
