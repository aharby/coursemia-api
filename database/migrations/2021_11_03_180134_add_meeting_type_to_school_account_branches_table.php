<?php

use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingTypeToSchoolAccountBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_account_branches', function (Blueprint $table) {
            $table->enum("meeting_type", array_keys(VCRProvidersEnum::getList()))->default(VCRProvidersEnum::AGORA);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_account_branches', function (Blueprint $table) {
            $table->dropColumn('meeting_type');
        });
    }
}
