<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportJobErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('import_job_errors')) {
            Schema::create('import_job_errors', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger("import_job_id");
                $table->foreign("import_job_id")->references("id")->on("import_jobs")->onDelete("cascade");

                $table->integer("row");
                $table->text("error");
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
        Schema::dropIfExists('import_job_errors');
    }
}
