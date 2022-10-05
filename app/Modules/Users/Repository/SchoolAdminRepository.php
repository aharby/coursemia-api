<?php


namespace App\Modules\Users\Repository;

use App\Modules\SchoolAdmin\Models\SchoolAdmin;
use App\Modules\Users\User;

class SchoolAdminRepository implements SchoolAdminRepositoryInterface
{

    public function create(array $data)
    {
        $user  = User::with('schoolAdmin')->find($data['user_id']);
        $user->schoolAdminAssignedSchools()->sync( $data['schools']);
        SchoolAdmin::updateOrCreate([
            'user_id' => $user->id,
        ],[
            'current_school_id' => $user->schoolAdminAssignedSchools->first()->id
        ]);
    }
    public function update(int $userId, array $data)
    {
        $user = User::with('schoolAdmin')->find($userId);
        $user->update($data);
        $user->schoolAdminAssignedSchools()->detach();
        $user->schoolAdminAssignedSchools()->sync( $data['schools']);
        SchoolAdmin::updateOrCreate([
            'user_id' => $user->id,
        ],[
            'current_school_id' => $user->schoolAdminAssignedSchools->first()->id
        ]);
    }

}
