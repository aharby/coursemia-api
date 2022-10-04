<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums;
class CreateResEssayQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_essay_questions', function (Blueprint $table) {
            $table->id();
            $table->longText('text')->nullable();
            $table->longText('question_feedback')->nullable();
            $table->longText('perfect_answers')->nullable();
            $table->string('image')->nullable();
            $table->decimal('time_to_solve', 8, 2)->nullable();
            $table->unsignedBigInteger('res_essay_data_id')->nullable()->index();
            $table->string("model")->default(QuestionModelsEnums::EXAM);
            $table->foreign('res_essay_data_id', 'res_essay_data_id_question_fr')
                ->references('id')->on('res_essay_data')->onDelete('SET NULL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('res_essay_questions');
    }
}
