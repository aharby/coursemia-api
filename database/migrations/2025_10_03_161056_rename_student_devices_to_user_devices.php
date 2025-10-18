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
        Schema::rename('student_devices', 'user_devices');

        // Rename column
        Schema::table('user_devices', function (Blueprint $table) {
            $table->renameColumn('student_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_devices', function (Blueprint $table) {
            //
        });
    }
};
