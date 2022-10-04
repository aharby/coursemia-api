<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesSubjectFormatSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_subject_format_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->mediumText('accept_criteria')->nullable();
            $table->tinyInteger('list_order_key')->default(0);

            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('is_editable')->default(1);


            $table->string('resource_slug',20)->nullable();

            $table->unsignedBigInteger('resource_id')->nullable()->index();
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('SET NULL');


            $table->unsignedBigInteger('subject_format_subject_id')->nullable()->index();
            $table->foreign('subject_format_subject_id','resource_subject_format_subject_s_f_s_id_foreign')->references('id')->on('subject_format_subject')->onDelete('SET NULL');


            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('SET NULL');
            $table->decimal('total_points',8,2)->default(0);

            $table->timestamp('paused_at')->nullable();

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
        Schema::dropIfExists('resource_subject_format_subject');
    }
}
