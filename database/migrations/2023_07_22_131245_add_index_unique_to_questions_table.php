<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexUniqueToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_translations', function (Blueprint $table) {
            $table->longText('title')->nullable()->change();
            $table->longText('description')->nullable()->change();
            $table->longText('explanation')->nullable()->change();
        });
        \Illuminate\Support\Facades\DB::unprepared('ALTER TABLE question_translations ADD UNIQUE key question_translations_title_unique (title(64))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            //
        });
    }
}
