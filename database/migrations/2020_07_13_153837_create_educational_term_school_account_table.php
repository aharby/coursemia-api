<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducationalTermSchoolAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educational_term_school_account', function (Blueprint $table) {
            $table->unsignedBigInteger('school_account_id');
            $table->unsignedBigInteger('educational_term_id');
            $table->foreign('school_account_id')->references('id')->on('school_accounts')
                ->onDelete('cascade');
            $table->foreign('educational_term_id')->references('id')->on('options')
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
        Schema::dropIfExists('educational_term_school_account');
    }
}
