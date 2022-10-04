<?php


namespace App\OurEdu\Roles\Repository;


use App\OurEdu\Roles\Role;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function all(): LengthAwarePaginator;


    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator;


    /**
     * @param int $id
     * @return Role|null
     */
    public function findOrFail(int $id): ?Role;

    public function find(int $id): ?Role;

    /**
     * @param array $data
     * @return Role
     */
    public function create(array $data): Role;


    /**
     * @param Role $role
     * @param array $data
     * @return bool
     */
    public function update(Role $role, array $data): bool;


    /**
     * @param Role $role
     * @return bool
     */
    public function delete(Role $role): bool;


    /**
     * @return array
     */
    public function pluck(): array;

    /**
     * @param string $type
     * @return array
     */
    public function pluckByType(string $type): array;

    /**
     * @param SchoolAccount $schoolAccount
     * @return Role|null
     */
    public function getSchoolDefaultRole(SchoolAccount $schoolAccount);

    /**
     * @param SchoolAccount $schoolAccount
     * @return array
     */
    public function pluckSchoolRoles(SchoolAccount $schoolAccount): array;

}
