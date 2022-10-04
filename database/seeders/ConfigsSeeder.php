<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigsSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment() != 'production') {
            DB::table('configs')->delete();
            DB::statement("ALTER TABLE configs AUTO_INCREMENT = 1");
            insertDefaultConfigs();
        }
    }
}
