<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentTestTimeToTableGeneralQuizStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quiz_students', function (Blueprint $table) {
            $table->decimal('student_test_duration',8,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quiz_students', function (Blueprint $table) {
            $table->dropColumn(['student_test_duration']);
        });
    }
}
