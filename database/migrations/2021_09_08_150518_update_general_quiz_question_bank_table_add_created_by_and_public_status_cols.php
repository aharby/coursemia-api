<?php

use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGeneralQuizQuestionBankTableAddCreatedByAndPublicStatusCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quiz_question_bank', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->enum('public_status',QuestionsPublicStatusesEnums::getPublicStatuses())->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quiz_question_bank', function (Blueprint $table) {
            $table->dropColumn('public_status');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
}
