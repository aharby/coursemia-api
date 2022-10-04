<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('import_jobs')){
            Schema::create('import_jobs', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger("classroom_id");
                $table->foreign("classroom_id")->references("id")->on("classrooms")->onDelete("cascade");

                $table->string("filename");
                $table->integer("status");
                $table->integer("has_errors")->default(0);
                $table->softDeletes();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_jobs');
    }
}
