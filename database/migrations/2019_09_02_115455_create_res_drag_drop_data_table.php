<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResDragDropDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_drag_drop_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('description')->nullable();
            $table->longText('question_feedback')->nullable();
            $table->decimal('time_to_solve',8,2)->nullable();

            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index();
            $table->foreign('resource_subject_format_subject_id')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');


            $table->unsignedBigInteger('drag_drop_type')->nullable()->index();
            $table->foreign('drag_drop_type')->references('id')->on('options')->onDelete('SET NULL');



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
        Schema::dropIfExists('res_drag_drop_data');
    }
}
