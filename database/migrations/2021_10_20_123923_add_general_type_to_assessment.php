<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneralTypeToAssessment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->boolean('assessor_type_is_general')->default(0);
            $table->boolean('assessee_type_is_general')->default(0);
            $table->boolean('assessment_viewer_type_is_general')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment', function (Blueprint $table) {
            $table->dropColumn(['assessor_type_is_general','assessee_type_is_general','assessment_viewer_type_is_general']);
        });
    }
}
