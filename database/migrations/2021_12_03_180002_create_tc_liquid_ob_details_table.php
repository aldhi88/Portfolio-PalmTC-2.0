<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcLiquidObDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_liquid_ob_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_liquid_ob_id');
            $table->bigInteger('tc_liquid_bottle_id');
            $table->smallInteger('bottle_liquid')->default(0);
            $table->smallInteger('bottle_oxidate')->default(0);
            $table->smallInteger('bottle_contam')->default(0);
            $table->smallInteger('bottle_other')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('tc_liquid_ob_details');
    }
}
