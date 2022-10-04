<?php

namespace Database\Seeders;

use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class StudentDataTransfer extends Seeder
{
    /**
     * @var Builder|Builder[]|Collection|Model|null
     */
    private $currentSchoolAccount;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->reset();
        // Boys school
        $boysBranches = [9, 10, 11, 12, 13, 14, 15, 16, 17, 22, 23, 24, 25, 30, 32, 34];
        $this->currentSchoolAccount = SchoolAccount::with('branches')->find(9);
        $boysBranchesUser = User::where('type', UserEnums::STUDENT_TYPE)->whereIn('branch_id', $boysBranches)->where('school_id', 1)->with('student')->cursor();

        $this->addStudent($boysBranchesUser);

        $girlsBranches = [5, 6, 7, 8, 18, 19, 20, 21, 26, 27, 28, 29, 31, 33, 35];
        $this->currentSchoolAccount = SchoolAccount::with('branches')->find(10);
        $boysBranchesUser = User::where('type', UserEnums::STUDENT_TYPE)->whereIn('branch_id', $girlsBranches)->where('school_id', 1)->with('student')->cursor();
        $this->addStudent($boysBranchesUser);
    }

    private function addStudent($users)
    {
        foreach ($users as $boyStudent) {
            $username = 'd' . $boyStudent->username;
            $newUser = $boyStudent->replicate()->fill([
                'school_id' => $this->currentSchoolAccount->id,
                'username' => $username,
                'password' => $username,
                'branch_id' => $this->currentSchoolAccount->branches->first()->id
            ]);
            $oldStudent = $boyStudent->student->toArray();
            // Save new User
            $newStudent = $oldStudent;
            $newStudent['password'] = $username;
            $classId = $oldStudent['class_id'];
            // class room
            $classroom = Classroom::whereHas('branchEducationalSystemGradeClass', function ($query) use ($classId) {
                $query->where('grade_class_id', $classId);
            })->where('branch_id', $this->currentSchoolAccount->branches->first()->id)
                ->first();
            if (is_null($classroom) && $oldStudent['class_id'] > 5 && $oldStudent['class_id'] < 37) {
                $classId = $oldStudent['class_id'] - 1;
                if ($oldStudent['class_id'] == 30) {
                    $classId = 28;
                }
                if ($oldStudent['class_id'] == 32) {
                    $classId = 24;
                }
                if ($oldStudent['class_id'] == 33) {
                    $classId = 26;
                }
                if ($oldStudent['class_id'] == 34) {
                    $classId = 28;
                }
                if ($oldStudent['class_id'] == 35) {
                    $classId = 26;
                }
                if ($oldStudent['class_id'] == 36) {
                    $classId = 28;
                }
                $classroom = Classroom::whereHas('branchEducationalSystemGradeClass', function ($query) use ($classId) {
                    $query->where('grade_class_id', $classId);
                })->where('branch_id', $this->currentSchoolAccount->branches->first()->id)
                    ->first();
            }
            if ($classroom) {
                $newUser->save();
                $newStudent['classroom_id'] = $classroom->id;
                $newStudent['class_id'] = $classId;
                unset($newStudent['id']);
                unset($newStudent['created_at']);
                unset($newStudent['updated_at']);
                unset($newStudent['user_id']);
                // save student
                $student = $newUser->student()->create($newStudent);
                // Subjects
                $subjects = Subject::where('educational_system_id', $oldStudent['educational_system_id'])
                    ->where('country_id', $newUser->country_id)
                    ->where('grade_class_id', $classId)
                    ->where('educational_term_id', 24)
                    ->where('academical_years_id', $oldStudent['academical_year_id'])
                    ->pluck('id');
                $student->subjects()->sync($subjects);
            } else {
                dump('classroom not found with data : grade class ' . $oldStudent['class_id'] . ' and branch id ' . $this->currentSchoolAccount->branches->first()->id);
                dump('current school account ' . $this->currentSchoolAccount . ' branch ' . $this->currentSchoolAccount->branches->first());
                dump('old student ' . $boyStudent);
            }
        }
    }

    private function reset()
    {
        Student::whereHas('user', function ($query) {
            $query->whereIn('school_id', [9, 10]);
        })->delete();
        User::whereIn('school_id', [9, 10])->delete();
    }
}
