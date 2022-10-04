<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\OurEdu\VCRSessions\Enums\ZoomHostStatusEnum;

class CreateZoomHostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_hosts', function (Blueprint $table) {
            $table->id();
            $table->string('zoom_user_id')->unique();
            $table->string('usage_status')->default(ZoomHostStatusEnum::AVAILABLE);
            $table->bigInteger('usages_number')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoom_hosts');
    }
}
