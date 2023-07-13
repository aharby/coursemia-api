<?php

namespace Database\Seeders;

use App\Modules\Settings\Models\Setting;
use Faker\Provider\Text;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keys = ['about_us','terms_and_conditions','privacy_policy','contact_us'];
        DB::table('settings')->truncate();
        foreach ($keys as $key){
            $setting = new Setting;
            $setting->key = $key;
            $setting->value = str_random(1000);
            $setting->save();
        }
    }
}
