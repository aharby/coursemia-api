<?php

namespace Database\Seeders;

use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Seeder;

class ModifyingStudentData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = Student::whereHas('user')->whereHas('classroom', function ($query) {
            $query->whereHas('branch', function ($query) {
                $query->whereHas('schoolAccount');
            });
        })->with(['user', 'classroom.branch'])->get();
        $i = 1;
        foreach ($students as $student) {
            dump($i);
            $i++;
            $student->user->update([
                'school_id' => $student->classroom->branch->school_account_id,
                'branch_id' => $student->classroom->branch->id
            ]);
        }
    }
}
