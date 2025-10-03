<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('orders', 'student_orders');

        Schema::table('student_orders', function (Blueprint $table) {
            $table->renameColumn('user_id', 'student_id');
        });
    }

    public function down()
    {
        Schema::table('student_orders', function (Blueprint $table) {
            $table->renameColumn('student_id', 'user_id');
        });

        Schema::rename('student_orders', 'orders');
    }

};
