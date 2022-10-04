<?php

namespace Database\Seeders;

use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExtendSessionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClassroomClassSession::where("from", ">=", "2021-02-20 00:00:00")->delete();
        VCRSession::where('time_to_start', ">=", "2021-02-20 00:00:00")
            ->where('vcr_session_type', 'school_session')->delete();
        ClassroomClass::query()->whereDoesntHave('sessions')->delete();
        $this->update();
    }


    private function update()
    {
        $classrooms = Classroom::cursor();
        foreach ($classrooms as $classroom) {
            $sessions = ClassroomClassSession::where('classroom_id', $classroom->id)
                ->where("from", ">=", '2021-02-12 00:00:00')
                ->where("to", "<=", '2021-02-19 00:00:00')
                ->cursor();
            foreach ($sessions as $session) {
                $isSunDay = (new Carbon($session->from))->isSunday();
                $isSaturDay = (new Carbon($session->from))->isSaturday();
                $isMonDay = (new Carbon($session->from))->isMonday();
                $isTuesDay = (new Carbon($session->from))->isTuesday();
                $isWednesday = (new Carbon($session->from))->isWednesday();
                $isThursday = (new Carbon($session->from))->isThursday();
                $isFriday = (new Carbon($session->from))->isFriday();
                $classroomClass = [
                    'classroom_id' => $classroom->id,
                    'subject_id' => $session->subject_id,
                    'instructor_id' => $session->instructor_id,
                    'from' => '2021-02-19',
                    'from_time' => $session->from_time,
                    'to' => null,
                    'to_time' => $session->to_time,
                    'until_date' => '2021-06-03',
                    'repeat' => 3,
                    'sun' => $isSunDay,
                    'mon' => $isMonDay,
                    'tue' => $isTuesDay,
                    'wed' => $isWednesday,
                    'thu' => $isThursday,
                    'fri' => $isFriday,
                    'sat' => $isSaturDay,
                ];
                try {
                    $classroomClass = ClassroomClass::create($classroomClass);
                    $classroomClass->createOrUpdateSessions();
                    DB::commit();
                } catch (ValidationException $e) {
                    dump($e->errors());
                } catch (Exception $e) {
                    DB::rollBack();
                    dump('error ' . $e->getMessage());
                }
            }
        }
    }
}
