<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;    

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            PermissionsEnum::MANAGE_USERS,
            PermissionsEnum::MANAGE_ROLES,
            PermissionsEnum::MANAGE_SETTINGS,
            PermissionsEnum::VIEW_ANALYTICS,
            PermissionsEnum::MANAGE_PAYMENTS,
            PermissionsEnum::CREATE_COURSE,
            PermissionsEnum::EDIT_COURSE,
            PermissionsEnum::DELETE_COURSE,
            PermissionsEnum::VIEW_COURSE,
            PermissionsEnum::VIEW_COURSE_CONTENT,
            PermissionsEnum::DELETE_COURSE_CONTENT,
            PermissionsEnum::CREATE_COURSE_CONTENT,
            PermissionsEnum::EDIT_COURSE_CONTENT,
            PermissionsEnum::LEAVE_REVIEW,
            PermissionsEnum::REPLY_REVIEWS,
            PermissionsEnum::MANAGE_DISCUSSIONS,
            PermissionsEnum::ENROLL_COURSE
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => RolesEnum::SUPER_ADMIN]);
        $admin = Role::firstOrCreate(['name' => RolesEnum::ADMIN]);
        $instructor = Role::firstOrCreate(['name' => RolesEnum::INSTRUCTOR]);
        $assistant = Role::firstOrCreate(['name' => RolesEnum::ASSISTANT]);
        $student = Role::firstOrCreate(['name' => RolesEnum::STUDENT]);
        $student = Role::firstOrCreate(['name' => RolesEnum::GUEST]);

        // Assign Permissions
        $admin->givePermissionTo(array_diff($permissions, [PermissionsEnum::MANAGE_PAYMENTS]));
        
        $instructor->givePermissionTo([
            PermissionsEnum::VIEW_COURSE,
            PermissionsEnum::EDIT_COURSE,
            PermissionsEnum::CREATE_COURSE_CONTENT,
            PermissionsEnum::VIEW_COURSE_CONTENT,
            PermissionsEnum::EDIT_COURSE_CONTENT,
            PermissionsEnum::DELETE_COURSE_CONTENT,
            PermissionsEnum::REPLY_REVIEWS,
            PermissionsEnum::MANAGE_DISCUSSIONS,
            PermissionsEnum::VIEW_ANALYTICS
        ]);

        $assistant->givePermissionTo([
            PermissionsEnum::VIEW_COURSE,
            PermissionsEnum::CREATE_COURSE_CONTENT,
            PermissionsEnum::VIEW_COURSE_CONTENT,
            PermissionsEnum::EDIT_COURSE_CONTENT,
            PermissionsEnum::REPLY_REVIEWS,
            PermissionsEnum::MANAGE_DISCUSSIONS
        ]);

        $student->givePermissionTo([
            PermissionsEnum::VIEW_COURSE_CONTENT,
            PermissionsEnum::ENROLL_COURSE
        ]);

    }
}
