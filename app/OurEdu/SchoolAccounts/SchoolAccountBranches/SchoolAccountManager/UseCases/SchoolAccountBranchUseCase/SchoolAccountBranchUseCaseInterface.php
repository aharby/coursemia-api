<?php

namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\SchoolAccountBranchUseCase;

interface SchoolAccountBranchUseCaseInterface
{
    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function update(array $data, $id): array ;

    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function updatePassword(array $data, $id): bool ;
    public function updateBranches(array $data, $id): bool;
    public function updateRole(array $data, $id): bool ;

}
