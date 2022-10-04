<?php

use App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelToResMultipleChoiceQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('res_multiple_choice_questions', function (Blueprint $table) {
            $table->string("model")->default(QuestionModelsEnums::EXAM)->after("res_multiple_choice_data_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('res_multiple_choice_questions', function (Blueprint $table) {
            $table->dropColumn("model");
        });
    }
}
