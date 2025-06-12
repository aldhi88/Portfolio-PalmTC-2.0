<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcRootingTransferBottlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_rooting_transfer_bottles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_rooting_ob_id');
            $table->bigInteger('tc_rooting_bottle_id');
            $table->smallInteger('bottle_rooting')->default(0);
            $table->smallInteger('leaf_rooting')->default(0);
            $table->smallInteger('bottle_left')->default(0);
            $table->smallInteger('leaf_left')->default(0);
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
        Schema::dropIfExists('tc_rooting_transfer_bottles');
    }
}
