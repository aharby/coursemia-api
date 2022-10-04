<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\UseCases\SchoolAccountUseCases;


interface SchoolAccountUseCaseInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function save(array $data): array;


    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function update(array $data, $id): bool ;
}
