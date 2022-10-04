<?php

namespace Database\Seeders;

use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Database\Seeder;

class AddPublishedBeforeToAssessments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Assessment::query()->whereNotNull('published_at')->update(['published_before'=>true]);
    }
}
