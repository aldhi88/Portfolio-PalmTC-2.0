<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcLiquidTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_liquid_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_liquid_bottle_id');
            $table->bigInteger('tc_liquid_ob_id')->nullable();
            $table->bigInteger('tc_liquid_transfer_id')->nullable();
            $table->bigInteger('tc_worker_id')->nullable();
            $table->smallInteger('first_total');
            $table->smallInteger('last_total');
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
        Schema::dropIfExists('tc_liquid_transactions');
    }
}
