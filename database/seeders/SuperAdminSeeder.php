<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Modules\Users\Models\User;
use App\Enums\RolesEnum; 
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding Super Admin...');
        
        $role = Role::exists(['name' => RolesEnum::SUPER_ADMIN]);
        
        if(!$role)
        {
            $this->command->warn('SuperAdminSeeder couldn\'t run');
            $this->command->warn('role super admin doesn\'t exist. Please run RolesAndPermissions seeders');
            return;
        }

        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');
        $name = env('SUPER_ADMIN_NAME');

        
        if(!$email || !$password || !$name)
        {
            $this->command->warn('SuperAdminSeeder couldn\'t run');
            $this->command->warn('super admin or password or name are not set in .env.');
            return;
        }

        $user = User::firstOrCreate(
    ['email' => $email], // lookup field(s)
        [
            'full_name' => $name,
            'password' => Hash::make($password),
            ]
        );

        if($user) {
            event(new Registered($user));

            $user->assignRole(RolesEnum::SUPER_ADMIN);
        }
    }
}
