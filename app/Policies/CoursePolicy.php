<?php

namespace App\Policies;

use App\Modules\Courses\Models\Course;

use App\Modules\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use App\Enums\RolesEnum;
class CoursePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Course $course)
    {
        if($user->hasRole(RolesEnum::ADMIN))
            return true;

        if($user->hasRole(RolesEnum::INSTRUCTOR))
            return $course->instructor_id === $user->id;

        if($user->hasRole(RolesEnum::ASSISTANT))
            return $user->instructor && $user->instructor->instructor_id === $course->instructor_id;

        return false;
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Course $course)
    {
        //
    }

    public function delete(User $user, Course $course)
    {
        //
    }

}
