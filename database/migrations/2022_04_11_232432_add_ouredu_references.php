<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOureduReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('educational_systems', function (Blueprint $table) {
            $table->uuid('our_edu_reference')->nullable();
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->uuid('our_edu_reference')->nullable();
        });

        Schema::table('grade_classes', function (Blueprint $table) {
            $table->uuid('our_edu_reference')->nullable();
        });
        Schema::table('options', function (Blueprint $table) {
            $table->uuid('our_edu_reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('educational_systems', function (Blueprint $table) {
            $table->dropColumn('our_edu_reference');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('our_edu_reference');
        });
        Schema::table('grade_classes', function (Blueprint $table) {
            $table->dropColumn('our_edu_reference');
        });
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('our_edu_reference');
        });
    }
}
