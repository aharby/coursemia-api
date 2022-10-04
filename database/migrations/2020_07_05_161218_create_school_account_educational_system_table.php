<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolAccountEducationalSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_account_educational_system', function (Blueprint $table) {
            $table->unsignedBigInteger('school_account_id')->nullable();
            $table->unsignedBigInteger('educational_system_id')->nullable();
            $table->foreign('school_account_id')->references('id')->on('school_accounts')
                ->onDelete('cascade');
            $table->foreign('educational_system_id')->references('id')->on('educational_systems')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_account_educational_system');
    }
}
