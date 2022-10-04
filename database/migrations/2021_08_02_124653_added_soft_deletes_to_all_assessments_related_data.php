<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddedSoftDeletesToAllAssessmentsRelatedData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('assessment_users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('assessment_points_rates', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('assessment_users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('assessment_points_rates', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
