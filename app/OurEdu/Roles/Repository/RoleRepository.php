<?php


namespace App\OurEdu\Roles\Repository;


use App\OurEdu\Roles\Role;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository implements RoleRepositoryInterface
{

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return Role::orderBy('id', 'DESC')->paginate(env('PAGE_LIMIT', 20));
    }

    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return Role::where('school_account_id' , auth()->user()->schoolAccount->id)->orderBy('id', 'DESC')->paginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param int $id
     * @return Role|null
     */
    public function findOrFail(int $id): ?Role
    {
        return Role::findOrFail($id);
    }
    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * @param array $data
     * @return Role
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * @param Role $role
     * @param array $data
     * @return bool
     */
    public function update(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    /**
     * @param Role $role
     * @return bool
     * @throws Exception
     */
    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    /**
     * @return array
     */
    public function pluck(): array
    {
        return Role::with('translations')->listsTranslations('title')->pluck('title', 'id')->toArray();
    }

    /**
     * @param string $type
     * @return array
     */
    public function pluckByType(string $type): array
    {
        return Role::where('type', $type)->with('translations')->listsTranslations('title')->pluck('title', 'id')->toArray();
    }

    /**
     * @param SchoolAccount $schoolAccount
     * @return array
     */
    public function pluckSchoolRoles(SchoolAccount $schoolAccount): array
    {
        return Role::query()
            ->where("school_account_id", "=", $schoolAccount->id)
            ->with('translations')
            ->listsTranslations('title')
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * @param SchoolAccount $schoolAccount
     * @return Role|null
     */
    public function getSchoolDefaultRole(SchoolAccount $schoolAccount)
    {
        return Role::query()->where("school_account_id", "=", $schoolAccount->id)->first();
    }
}
