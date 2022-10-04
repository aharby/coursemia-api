<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralQuizSubjectFormatSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_quiz_subject_format_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("subject_format_subject_id");
            $table->unsignedBigInteger("general_quiz_id");

            $table->foreign("subject_format_subject_id", "subject_format_subject_id_foreign")->references("id")->on("subject_format_subject")->onDelete("cascade");
            $table->foreign("general_quiz_id")->references("id")->on("general_quizzes")->onDelete("cascade");

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
        Schema::dropIfExists('general_quiz_subject_format_subject');
    }
}
