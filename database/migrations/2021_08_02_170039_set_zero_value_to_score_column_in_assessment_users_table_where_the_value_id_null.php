<?php

use App\OurEdu\Assessments\Models\AssessmentUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetZeroValueToScoreColumnInAssessmentUsersTableWhereTheValueIdNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AssessmentUser::query()
            ->whereNull("score")
            ->update(["score" => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
