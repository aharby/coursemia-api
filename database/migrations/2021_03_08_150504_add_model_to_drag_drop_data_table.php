<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelToDragDropDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('res_drag_drop_data', function (Blueprint $table) {
            $table->string("model")->default(\App\OurEdu\ResourceSubjectFormats\Enums\QuestionModelsEnums::EXAM);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('res_drag_drop_data', function (Blueprint $table) {
            $table->dropColumn("model");
        });
    }
}
