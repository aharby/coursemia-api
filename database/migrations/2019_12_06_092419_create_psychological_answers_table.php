<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsychologicalAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychological_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('psychological_test_id')->index();
            $table->unsignedBigInteger('psychological_question_id')->index()->nullable();
            $table->unsignedBigInteger('psychological_option_id')->index()->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->foreign('psychological_test_id')
                ->references('id')
                ->on('psychological_tests')
                ->onDelete('cascade');

            $table->foreign('psychological_question_id')
                ->references('id')
                ->on('psychological_questions')
                ->onDelete('cascade');

            $table->foreign('psychological_option_id')
                ->references('id')
                ->on('psychological_options')
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
        Schema::dropIfExists('psychological_answers');
    }
}
