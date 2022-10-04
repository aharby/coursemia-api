<?php

namespace Database\Seeders;

use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use Illuminate\Database\Seeder;

class RemoveDeletedResourcesFromPreparedQuestions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get prepared exams and general exams questions that doesn't have question or question deleted
        PrepareExamQuestion::doesntHave('question')->delete();
        PreparedGeneralExamQuestion::doesntHave('questionable')->delete();
    }
}
