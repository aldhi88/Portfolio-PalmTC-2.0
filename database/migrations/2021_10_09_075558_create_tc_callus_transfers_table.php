<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcCallusTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_callus_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_callus_ob_id');
            $table->bigInteger('tc_worker_id');
            $table->bigInteger('tc_laminar_id');
            $table->integer('bottle_used');
            $table->integer('new_bottle');
            $table->dateTime('date_work');
            $table->integer('time_work')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('tc_callus_transfers');
    }
}
