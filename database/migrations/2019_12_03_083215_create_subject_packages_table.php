<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('picture', 190)->nullable();
            $table->text('description')->nullable();
            $table->string('price')->nullable();
            $table->tinyInteger('is_active')->default(0);

            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL');

            $table->bigInteger('grade_class_id')->unsigned()->nullable();
            $table->foreign('grade_class_id')->references('id')->on('grade_classes')->onDelete('cascade');

            $table->bigInteger('educational_system_id')->unsigned()->nullable();
            $table->foreign('educational_system_id')->references('id')->on('educational_systems')->onDelete('cascade');

            $table->bigInteger('academical_years_id')->unsigned()->nullable();
            $table->foreign('academical_years_id')->references('id')->on('options')->onDelete('cascade');

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
        Schema::dropIfExists('subject_packages');
    }
}
