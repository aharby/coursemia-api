<?php

use App\OurEdu\Config\Config;
use Illuminate\Database\Migrations\Migration;

class InsertMeetingTypeToConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $row = [
            'field_type' => 'text',
            'field_class' => '',
            'type' => 'Basic Information',
            'field' => 'meeting_type',
            'label:ar' => 'نوع الاجتماع',
            'label:en' => 'Meeting Type',
            'value:ar' => 'agora',
            'value:en' => 'agora',
            'created_by' => 2,
        ];

        Config::query()->create($row);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Config::query()
            ->where("field", "=", "meeting_type")
            ->delete();
    }
}
