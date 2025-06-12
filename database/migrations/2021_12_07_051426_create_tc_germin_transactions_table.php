<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcGerminTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_germin_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_germin_bottle_id');
            $table->bigInteger('tc_germin_ob_id')->nullable();
            $table->bigInteger('tc_germin_transfer_id')->nullable();
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
        Schema::dropIfExists('tc_germin_transactions');
    }
}
