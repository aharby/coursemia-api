<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');


            $table->string('name')->nullable();


            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('out_of_date')->default(0);

            $table->string('subscription_cost')->nullable();
            $table->text('subject_library_text')->nullable();
            $table->json('subject_library_attachment')->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->string('section_type', 20)->nullable();
            $table->string('image')->nullable();
            $table->string('color')->nullable();


            $table->bigInteger('educational_system_id')->unsigned()->nullable();
            $table->foreign('educational_system_id')->references('id')->on('educational_systems')->onDelete('SET NULL');

            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL');



            $table->bigInteger('grade_class_id')->unsigned()->nullable();
            $table->foreign('grade_class_id')->references('id')->on('grade_classes')->onDelete('cascade');


            $table->bigInteger('educational_term_id')->unsigned()->nullable();
            $table->foreign('educational_term_id')->references('id')->on('options')->onDelete('cascade');


            $table->bigInteger('academical_years_id')->unsigned()->nullable();
            $table->foreign('academical_years_id')->references('id')->on('options')->onDelete('cascade');

            $table->bigInteger('sme_id')->unsigned()->nullable();
            $table->foreign('sme_id')->references('id')->on('users')->onDelete('SET NULL');


            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('SET NULL');

            $table->bigInteger('practices_number')->default(0);
            $table->decimal('total_points', 8, 2)->default(0);
            $table->tinyInteger('is_aptitude')->default(0);

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
        Schema::dropIfExists('subjects');
    }
}
