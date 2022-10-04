<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessorResultViewerAvgscore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessor_result_viewer_avgscore', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id')->nullable()->index();
            $table->foreign('assessment_id')->references('id')
                ->on('assessments')->onDelete('CASCADE');

            $table->unsignedBigInteger('assessor_id')->nullable()->index();
            $table->foreign('assessor_id')->references('id')
                ->on('users')->onDelete('CASCADE');

            $table->unsignedBigInteger('viewer_id')->nullable()->index();
            $table->foreign('viewer_id')->references('id')
                ->on('users')->onDelete('CASCADE');

            $table->float("average_score")->default(0);

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
        Schema::dropIfExists('assessor_result_viewer_avgscore');
    }
}
