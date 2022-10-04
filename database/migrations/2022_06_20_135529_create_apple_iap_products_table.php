<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppleIapProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apple_iap_products', function (Blueprint $table) {
            $table->unsignedBigInteger("product_id")->primary();
            $table->string("title");
            $table->text("description")->nullable();
            $table->float("price");
            $table->string("currency", 3)->default("SAR");
            $table->timestamp('verified_at')->nullable();
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
        Schema::dropIfExists('apple_iap_products');
    }
}
