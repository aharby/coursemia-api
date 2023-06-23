<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use App\Modules\Users\Admin\Models\Admin;
use App\Modules\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        $admin = new Admin;
        $admin->name = 'Admin';
        $admin->email = 'admin@admin.com';
        $admin->password = Hash::make('password');
        $admin->is_active = 1;
        $admin->save();
    }
}
