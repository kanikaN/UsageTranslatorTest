<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableChargeable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chargeable', function (Blueprint $table) {
            $table->id();
            $table->integer('partnerID')->nullable(false);
            $table->string('product');
            $table->string('partnerPurchasedPlanID');
            $table->string('plan');
            $table->integer('usage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chargeable');
    }
}
