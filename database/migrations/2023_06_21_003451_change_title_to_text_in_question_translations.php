<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTitleToTextInQuestionTranslations extends Migration
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_in_question_translations', function (Blueprint $table) {
            //
        });
    }
}
