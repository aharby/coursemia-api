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
        Schema::table('cart_courses', function (Blueprint $table) {
            $table->dropForeign('cart_items_user_id_foreign');
            $table->dropForeign('cart_items_course_id_foreign');
            $table->dropIndex('cart_items_user_id_foreign');
            $table->dropIndex('cart_items_course_id_foreign');

            $table->unsignedBigInteger('user_id')->nullable()->change();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();

            $table->foreignId('guest_device_id')
                    ->nullable()
                    ->references('id')->on('guest_devices')
                    ->cascadeOnDelete();
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
