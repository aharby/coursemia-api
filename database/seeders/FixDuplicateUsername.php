<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixDuplicateUsername extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $duplicated = DB::select('SELECT username, COUNT(*) c FROM users WHERE username is not null && deleted_at is null GROUP BY username HAVING c > 1');
        foreach ($duplicated as $row) {
            User::where('username', $row->username)->where('type', UserEnums::PARENT_TYPE)->delete();
            $students = User::where('username', $row->username)->where('type', UserEnums::STUDENT_TYPE)->first();
            if ($students) {
                $students->delete();
            }
        }
    }
}
