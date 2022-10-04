<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_classes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->tinyInteger('is_active')->default(0);

            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')
                ->references('id')->on('countries')->onDelete('SET NULL');

            $table->bigInteger('educational_system_id')->unsigned()->nullable();
            $table->foreign('educational_system_id')
                ->references('id')->on('educational_systems')->onDelete('SET NULL');

            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('SET NULL');

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
        Schema::dropIfExists('grade_classes');
    }
}
