<?php

namespace Database\Seeders;

use App\OurEdu\Assessments\Models\AssessmentUser;
use Illuminate\Database\Seeder;

class addCountedToAsssesseUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AssessmentUser::query()->where('counted',false)->update(['counted'=>true]);
    }
}
