<?php


namespace App\Modules\Users\Repository;


use App\Modules\Users\Models\Admin;

interface AdminRepositoryInterface
{
    public function create(array $data): ?admin;

    public function findOrFail(int $id): ?admin;

    public function update(Admin $admin, array $data): bool;

    public function delete(Admin $admin) : bool;

    public function getAdminByUserId (int $userId): ?admin;
}
