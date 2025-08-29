<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_correct_answers')->default(0);
            $table->string('stripe_customer_id')->nullable();
            $table->timestamps();
        });

        // Move data from users to students
        DB::statement("
            INSERT INTO students (user_id, total_correct_answers, stripe_customer_id, created_at, updated_at)
            SELECT id, total_correct_answers, stripe_customer_id, NOW(), NOW() FROM users
        ");

        // Remove columns from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_correct_answers', 'stripe_customer_id']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('total_correct_answers')->default(0);
            $table->string('stripe_customer_id')->nullable();
        });

        Schema::dropIfExists('students');
    }

};
