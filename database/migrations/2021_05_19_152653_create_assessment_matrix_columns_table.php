<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentMatrixColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assess_matrix_columns', function (Blueprint $table) {
            $table->id();
            $table->longText('text')->nullable();
            $table->float("grade")->default(0);
            $table->unsignedBigInteger('assess_data_id')->nullable()->index();
            $table->foreign('assess_data_id')->references('id')
                    ->on('assess_matrix_data')->onDelete('SET NULL');
            $table->softDeletes();
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
        Schema::dropIfExists('assess_matrix_columns');
    }
}
