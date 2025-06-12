<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcMediumStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_medium_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_bottle_id');
            $table->bigInteger('tc_agar_id');
            $table->bigInteger('tc_medium_id');
            $table->bigInteger('tc_worker_id');
            $table->integer('stock');
            $table->boolean('status')->nullable();
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
        Schema::dropIfExists('tc_medium_stocks');
    }
}
