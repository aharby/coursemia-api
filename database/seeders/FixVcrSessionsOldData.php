<?php

namespace Database\Seeders;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Database\Seeder;

class FixVcrSessionsOldData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vcrSessions = VCRSession::doesntHave('classroomClassSession')->cursor();

        foreach ($vcrSessions as $vcrSession) {
            dump($vcrSession->delete());
        }
    }
}
