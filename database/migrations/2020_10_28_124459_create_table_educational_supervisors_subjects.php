<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEducationalSupervisorsSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_supervisors_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('edu_supervisor_id')->index();
            $table->unsignedBigInteger('edu_system_id')->index();
            $table->unsignedBigInteger('grade_class_id')->index();
            $table->unsignedBigInteger('subject_id')->index();

            $table->foreign('edu_supervisor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');

            $table->foreign('grade_class_id')
                ->references('id')
                ->on('grade_classes')
                ->onDelete('cascade');

            $table->foreign('edu_system_id')
                ->references('id')
                ->on('educational_systems')
                ->onDelete('cascade');


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
        Schema::dropIfExists('edu_supervisors_subjects');
    }
}
