<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchEducationalSystemGradeClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_educational_system_grade_class', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_educational_system_id')->nullable();
            $table->unsignedBigInteger('grade_class_id')->nullable();
            $table->foreign('branch_educational_system_id','branch_edu_sys_grade_class_branch_edu_sys_id_fr')->references('id')->on('branch_educational_system')
                ->onDelete('cascade');
            $table->foreign('grade_class_id')->references('id')->on('grade_classes')
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
        Schema::dropIfExists('branch_educational_system_grade_class');
    }
}
