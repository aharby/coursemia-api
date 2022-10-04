<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->nullable()->index();
            $table->string('name');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('subject_id')
                ->nullable();
            $table->unsignedBigInteger('difficulty_level_id')
                ->nullable();

            $table->boolean('is_active')->default(false);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('difficulty_level_id')
                ->references('id')
                ->on('options')
                ->onDelete('SET NULL');

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('SET NULL');

            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_exams');
    }
}
