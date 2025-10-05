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
        $roles = getEnumValues(RolesEnum::class);

        foreach ( $roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $permissions = getEnumValues(PermissionsEnum::class);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Assign Permissions
        $adminPermissions = array_diff($permissions, [PermissionsEnum::MANAGE_PAYMENTS]);

        Role::where('name', RolesEnum::ADMIN)->first()
            ->givePermissionTo($adminPermissions);

        Role::where('name', RolesEnum::INSTRUCTOR)->first()
            ->givePermissionTo([
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

        Role::where('name', RolesEnum::ASSISTANT)->first()
            ->givePermissionTo([
                PermissionsEnum::VIEW_COURSE,
                PermissionsEnum::CREATE_COURSE_CONTENT,
                PermissionsEnum::VIEW_COURSE_CONTENT,
                PermissionsEnum::EDIT_COURSE_CONTENT,
                PermissionsEnum::REPLY_REVIEWS,
                PermissionsEnum::MANAGE_DISCUSSIONS
            ]);

        Role::where('name', RolesEnum::STUDENT)->first()
            ->givePermissionTo([
                PermissionsEnum::VIEW_COURSE_CONTENT,
                PermissionsEnum::ENROLL_COURSE
            ]);
    }
}
