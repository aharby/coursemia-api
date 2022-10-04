<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsSubjectsQuestionsBankPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools_subjects_questions_bank_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("school_id");
            $table->unsignedBigInteger("subject_id");
            $table->tinyInteger("branch_scope")->default(false);
            $table->tinyInteger("grade_scope")->default(false);
            $table->tinyInteger("school_scope")->default(false);

            $table->foreign('school_id')
                ->references('id')
                ->on('school_accounts')
                ->onDelete('CASCADE');

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('CASCADE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schools_subjects_questions_bank_permissions');
    }
}
