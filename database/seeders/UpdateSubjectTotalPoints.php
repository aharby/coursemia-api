<?php

namespace Database\Seeders;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use Illuminate\Database\Seeder;

class UpdateSubjectTotalPoints extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subjects = Subject::all();

        foreach ($subjects as $subject) {
            $subjectRepository = new SubjectRepository($subject);

            $subjectRepository->updateTotalPoints();
        }
    }
}
