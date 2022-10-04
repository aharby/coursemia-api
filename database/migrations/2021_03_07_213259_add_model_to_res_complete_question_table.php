<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelToResCompleteQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('res_complete_questions', function (Blueprint $table) {
            $table->string("model")->default(\App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums::EXAM)->after("res_complete_data_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('res_complete_questions', function (Blueprint $table) {
            $table->dropColumn("model");
        });
    }
}
