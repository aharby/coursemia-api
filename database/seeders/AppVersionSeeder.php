<?php

namespace Database\Seeders;

use App\OurEdu\AppVersions\AppVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('version')->nullable();
         * */
        DB::table('app_versions')->delete();
        DB::statement("ALTER TABLE app_versions AUTO_INCREMENT = 1");
        $rows = [];


        $rows[] = [
            'type' => 'android-current',
            'name' => 'android',
            'version' => '1.0',
        ];;

        ///////////////// Logo
        $rows[] = [
            'type' => 'android-next',
            'name' => 'android',
            'version' => '1.1',
        ];

        $rows[] = [
            'type' => 'ios-current',
            'name' => 'ios',
            'version' => '1.0',
        ];;

        ///////////////// Logo
        $rows[] = [
            'type' => 'ios-next',
            'name' => 'ios',
            'version' => '1.1',
        ];

        foreach ($rows as $row) {
            AppVersion::create($row);
        }
    }
}
