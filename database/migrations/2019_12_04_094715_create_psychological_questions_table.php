<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsychologicalQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychological_questions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('psychological_test_id')
                ->index()
                ->nullable();

            $table->tinyInteger('is_active')->default(0);

            $table->bigInteger('created_by')->unsigned()->nullable();

            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('psychological_test_id')
                ->references('id')->on('psychological_tests')->onDelete('SET NULL');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psychological_questions');
    }
}
