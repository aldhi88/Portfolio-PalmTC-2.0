<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcCallusTransferStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_callus_transfer_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_callus_transfer_id');
            $table->bigInteger('tc_medium_stock_id');
            $table->integer('stock_used');
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
        Schema::dropIfExists('tc_callus_transfer_stocks');
    }
}
