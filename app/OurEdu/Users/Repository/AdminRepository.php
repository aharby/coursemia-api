<?php


namespace App\OurEdu\Users\Repository;


use App\OurEdu\Users\Models\Admin;
use App\OurEdu\Users\User;

class AdminRepository implements AdminRepositoryInterface
{

    public function create(array $data): ?admin
    {
        return ContentAuthor::create($data);
    }

    public function findOrFail(int $id): ?admin
    {
        return Admin::findOrFail($id);
    }

    public function update(Admin $admin, array $data): bool
    {
        return $admin->update($data);
    }

    public function delete(Admin $admin): bool
    {
        return $admin->delete();
    }

    public function getAdminByUserId(int $userId): ?admin
    {
        return Admin::where('user_id',$userId)->firstOrFail();
    }

}
