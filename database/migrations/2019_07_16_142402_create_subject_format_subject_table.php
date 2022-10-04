<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectFormatSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_format_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject_type',30)->nullable();
            $table->tinyInteger('list_order_key')->default(0);

            $table->string('title')->nullable();
            $table->string('slug',80)->nullable();
            $table->longText('description')->nullable();
            $table->tinyInteger('is_active')->default(0);

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->unsignedBigInteger('parent_subject_format_id')->nullable()->index();
            $table->foreign('parent_subject_format_id')->references('id')->on('subject_format_subject')->onDelete('SET NULL');


            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('SET NULL');
            $table->tinyInteger('is_editable')->default(1);

            $table->tinyInteger('has_data_resources')->default(0);

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
        Schema::dropIfExists('subject_format_subject');
    }
}
