<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostCourseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_course_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('about_course',2000)->nullable();


            $table->unsignedBigInteger('country_id')->index();
            $table->unsignedBigInteger('speciality_id')->index();

            $table->foreign('country_id')->references('id')->on('countries')
                ->onDelete('cascade');
            $table->foreign('speciality_id')->references('id')->on('specialities')
                ->onDelete('cascade');
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
        Schema::dropIfExists('host_course_requests');
    }
}
