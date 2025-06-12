<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcInitBottlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_init_bottles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->smallInteger('block_number');
            $table->smallInteger('bottle_number');
            $table->bigInteger('tc_worker_id');
            $table->bigInteger('tc_laminar_id');
            $table->bigInteger('tc_medium_stock_id');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('tc_init_bottles');
    }
}
