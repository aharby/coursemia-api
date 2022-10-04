<?php

namespace Database\Seeders;

use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\BranchEducationalSystemGradeClass;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTwoHundredMeetingZoomInSameTime extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branchEducationalSystem = BranchEducationalSystem::where('branch_id', 37)
            ->where('educational_system_id', 1)
            ->where('academic_year_id', 25)
            ->where('educational_term_id', 24)
            ->first();

        $branchEducationalSystemId = $branchEducationalSystem->id;
        $branchEduSysGradeClass = BranchEducationalSystemGradeClass::where('grade_class_id', 6)
            ->where('branch_educational_system_id', $branchEducationalSystemId)->first();


        $branchEduSysGradeClassId = $branchEduSysGradeClass->id;
        $gradeClassId = $branchEduSysGradeClass->gradeClass;
        $countryId = $branchEducationalSystem->branch->schoolAccount->country_id;
        DB::beginTransaction();
        try {

            for ($i = 0; $i < 499; $i++) {
                // Create Classroom
                $newClassRoom = Classroom::query()->firstOrCreate([
                    'name' => 'test 200 concurrent session no ' . $i + 1,
                    'branch_id' => 37,
                    'branch_edu_sys_grade_class_id' => $branchEduSysGradeClassId,
                ]);

//                $this->createStudent($i,$branchEducationalSystem,$countryId,$newClassRoom,$gradeClassId);

                $instructorId = $this->createInstructor($i, $branchEducationalSystem->branch_id,$countryId);

                $this->createSession($newClassRoom,344,$instructorId);

            }
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            dump($exception->getMessage(),$exception->getLine(),$exception->getCode(),$exception->getTrace());
        }
    }

    private function createSession(Classroom $classroom,$subjectId,$instructorId)
    {
        $data = [
            'subject_id' => $subjectId,
            'classroom_id' => $classroom->id,
            'instructor_id' => $instructorId,
            'repeat' => 0,
            'from' => '2022-03-13',
            'from_time' => '05:05:00',
            'to_time' => '05:07:00'
        ];
        $classroomClass = $classroom->classroomClass()->create($data);
        $classroomClass->createOrUpdateSessions();
    }

    private function createStudent($i,$branchEducationalSystem,$countryId,$newClassroom,$gradeClassId)
    {
        $username = 'testStudent' . $i + 1;
        $user = User::create(
            [
                'first_name' => Str::random('5'),
                'last_name' => Str::random('5'),
                'username' => $username,
                'language' => 'ar',
                'password' => $username,
                'type' => UserEnums::STUDENT_TYPE,
                'is_active' => 1,
                'confirmed' => 1,
                'country_id' => $countryId,
                'branch_id' => $branchEducationalSystem->branch_id,
                'school_id' => $branchEducationalSystem->branch->schoolAccount->id
            ]
        );
        $student = Student::create(
            [
                'user_id' => $user->id,
                'classroom_id' => $newClassroom->id,
                'password' => $username,
                'educational_system_id' => $branchEducationalSystem->educational_system_id,
                'academical_year_id' => $branchEducationalSystem->academic_year_id,
                'class_id' => $gradeClassId->id,
            ]
        );
        $subjects = Subject::where('educational_system_id', $branchEducationalSystem->educational_system_id)
            ->where('country_id', $countryId)
            ->where('grade_class_id', $gradeClassId->id)
            ->where('educational_term_id', $branchEducationalSystem->educational_term_id)
            ->where('academical_years_id', $branchEducationalSystem->academic_year_id)
            ->pluck('id')->toArray();
        $student->subjects()->sync($subjects);
    }

    private function createInstructor($i, $branchId,$countryId)
    {
        $username = 'teIns' . $i + 1;
        $schoolInstructorUser = User::query()->firstOrCreate([
            'first_name' => Str::random('5'),
            'last_name' => Str::random('5'),
            'username' => $username,
            'language' => 'ar',
            'password' => $username,
            'type' => UserEnums::SCHOOL_INSTRUCTOR,
            'is_active' => 1,
            'confirmed' => 1,
            'country_id' => $countryId,
            'branch_id' => $branchId,
        ]);
        $schoolInstructorUser->schoolInstructorSubjects()->syncWithoutDetaching(344);
        return $schoolInstructorUser->id;
    }
}
