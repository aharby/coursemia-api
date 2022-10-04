<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentAuthorTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_author_task', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('task_id')->nullable()->index();
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('SET NULL');

            $table->unsignedBigInteger('content_author_id')->nullable()->index();
            $table->foreign('content_author_id')->references('id')->on('content_authors')->onDelete('SET NULL');

            $table->unique(['task_id','content_author_id','deleted_at']);

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
        Schema::dropIfExists('content_author_task');
    }
}
