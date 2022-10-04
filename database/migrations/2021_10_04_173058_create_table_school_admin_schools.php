<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSchoolAdminSchools extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_admin_schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('SET NULL');
            $table->unsignedBigInteger('school_account_id')->nullable()->index();
            $table->foreign('school_account_id')->references('id')
                ->on('school_accounts')->onDelete('SET NULL');
            $table->boolean('current')->default(false);

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
        Schema::dropIfExists('school_admin_schools');
    }
}
