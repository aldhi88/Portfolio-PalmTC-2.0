<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcEmbryoBottlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_embryo_bottles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_callus_transfer_id')->nullable();
            $table->bigInteger('tc_embryo_transfer_id')->nullable();
            $table->bigInteger('tc_worker_id');
            $table->bigInteger('tc_laminar_id');
            $table->tinyInteger('sub')->default(1);
            $table->integer('number_of_bottle');
            $table->tinyInteger('status')->default(1);
            $table->text('desc')->nullable();
            $table->dateTime('bottle_date');
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
        Schema::dropIfExists('tc_embryo_bottles');
    }
}
