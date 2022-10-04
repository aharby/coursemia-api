<?php

namespace Database\Seeders;

use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Database\Seeder;

class DeleteTrashedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClassroomClassSession::onlyTrashed()->forceDelete();

        VCRSession::onlyTrashed()->forceDelete();
    }
}
