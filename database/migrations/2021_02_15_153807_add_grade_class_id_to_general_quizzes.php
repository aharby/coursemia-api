<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradeClassIdToGeneralQuizzes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('grade_class_id')->nullable();
            $table->foreign('grade_class_id')->references('id')
                ->on('grade_classes')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quizzes', function (Blueprint $table) {
            $table->dropForeign('general_quizzes_grade_class_id_foreign');
            $table->dropColumn('grade_class_id');
        });
    }
}
