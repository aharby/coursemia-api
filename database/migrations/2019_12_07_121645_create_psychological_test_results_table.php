<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsychologicalTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychological_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('percentage');
            $table->unsignedBigInteger('psychological_test_id')
                ->index();
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->unsignedBigInteger('psychological_recomendation_id')
                ->index()
                ->nullable();
            $table->timestamps();

            $table->foreign('psychological_test_id')
                ->references('id')
                ->on('psychological_tests')
                ->onDelete('cascade');

            $table->foreign('psychological_recomendation_id')
                ->references('id')
                ->on('psychological_recomendations')
                ->onDelete('SET NULL');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('psychological_results');
    }
}
