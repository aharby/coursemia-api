<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users', function (Blueprint $table)  {
            $table->string('language')->default(config('app.locale'))->change();

            $table->string('full_name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('password')->nullable()->change();

            $table->boolean('is_verified')->default(false)->change();
            $table->boolean('is_active')->default(true)->change();

            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->string('country_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
