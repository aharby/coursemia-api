<?php

namespace Database\Seeders;

use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use Illuminate\Database\Seeder;

class FixVcvTime extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classroomClasses = ClassroomClassSession::whereHas('vcrSession')
            ->with('vcrSession')->where('from', '>=', '2020-10-26 00:00:00')
            ->where('to', '<=', '2020-10-28 00:00:00')->get();
        //        dd($classroomClasses->count());
        foreach ($classroomClasses as $classroomClass) {

            if ($classroomClass->from != $classroomClass->vcrSession->time_to_start) {
                dump((string)$classroomClass->vcrSession->time_to_start, (string)$classroomClass->from);
                dump('----');
            }
            //                $classroomClass->vcrSession->update([
            //                    'time_to_start'=>$classroomClass->from,
            //                    'time_to_end' => $classroomClass->to
            //                ]);

        }
    }
}
