<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVcrSupportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_supports', function (Blueprint $table) {
            $table->id();
            $table->text('message')->nullable();
            $table->string('agora_log_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id')->onDelete('SET NULL');
            $table->unsignedBigInteger('school_account_branch_id')->nullable();
            $table->foreign('school_account_branch_id')->on('school_account_branches')->references('id')->onDelete('SET NULL');
            $table->json('session_info');
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
        Schema::dropIfExists('vcr_supports');
    }
}
