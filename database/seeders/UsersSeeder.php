<?php

namespace Database\Seeders;

use App\Modules\Country\Models\Country;
use App\Modules\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        $country = Country::find(63);
        for ($i = 0; $i < 10; $i++){
            $user = User::first();
            if (!isset($user)){
                $phone = "01000000000";
            }else{
                $phone = "0100000000$i";
            }
            $email = "user$i@gmail.com";
            $user = [
                'full_name' => "Test User ".$i,
                'email' => $email,
                'phone' => $phone,
                'country_id' => $country->id,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'refer_code'    => Str::random(8)
            ];
            DB::table('users')->insert($user);
        }
    }
}
