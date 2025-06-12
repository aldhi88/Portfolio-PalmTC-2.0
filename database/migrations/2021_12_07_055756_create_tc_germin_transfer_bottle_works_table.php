<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcGerminTransferBottleWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_germin_transfer_bottle_works', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_germin_transfer_id');
            $table->bigInteger('tc_germin_transfer_bottle_id');
            $table->mediumInteger('first_total');
            $table->mediumInteger('total_work');
            $table->mediumInteger('back_bottle');
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
        Schema::dropIfExists('tc_germin_transfer_bottle_works');
    }
}
