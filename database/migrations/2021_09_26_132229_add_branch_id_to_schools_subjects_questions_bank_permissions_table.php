<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToSchoolsSubjectsQuestionsBankPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools_subjects_questions_bank_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger("branch_id")->nullable();
            $table->foreign("branch_id")
                ->references('id')
                ->on('school_account_branches')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schools_subjects_questions_bank_permissions', function (Blueprint $table) {
            $table->dropColumn("branch_id");
        });
    }
}
