<?php

namespace App\Policies;

use App\Modules\Courses\Models\Course;

use App\Modules\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use App\Enums\RolesEnum;
use App\Enums\PermissionsEnum;

class CoursePolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_COURSE);
    }

    public function update(User $user, Course $course)
    {
        if(!$user->can(PermissionsEnum::EDIT_COURSE))
            return false;

        if($user->hasRole(RolesEnum::ADMIN))
            return true;

        if($user->hasRole(RolesEnum::INSTRUCTOR))
            return $course->instructor_id === $user->id;

        return false;
    }

    public function delete(User $user, Course $course)
    {
        return $user->can(PermissionsEnum::DELETE_COURSE);
    }

}
