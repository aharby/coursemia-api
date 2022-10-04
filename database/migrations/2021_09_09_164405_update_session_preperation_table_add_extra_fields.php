<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSessionPreperationTableAddExtraFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_preparations', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->nullable();
            $table->foreign('section_id')
                ->references('id')->on('subject_format_subject')
                ->onDelete('SET NULL');
            $table->longText('objectives')->nullable();
            $table->renameColumn('student_preparation', 'pre_Learning');
            $table->longText('introductory')->nullable();
            $table->longText('application')->nullable();
            $table->longText('evaluation')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session_preparations', function(Blueprint $table) {
            $table->renameColumn('pre_Learning', 'student_preparation');
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
            $table->dropColumn('evaluation');
            $table->dropColumn('application');
            $table->dropColumn('introductory');
            $table->dropColumn('objectives');
        });
    }
}
