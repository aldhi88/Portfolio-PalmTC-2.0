<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcRootingTransferBottleWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_rooting_transfer_bottle_works', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_rooting_transfer_id');
            $table->bigInteger('tc_rooting_transfer_bottle_id');
            $table->mediumInteger('first_total');
            $table->mediumInteger('first_leaf');
            $table->mediumInteger('total_work');
            $table->mediumInteger('leaf_work');
            $table->mediumInteger('back_bottle');
            $table->mediumInteger('back_leaf');
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
        Schema::dropIfExists('tc_rooting_transfer_bottle_works');
    }
}
