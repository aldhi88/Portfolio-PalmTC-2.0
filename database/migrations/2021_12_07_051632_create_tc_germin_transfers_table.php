<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcGerminTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_germin_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_worker_id');
            $table->bigInteger('tc_laminar_id');
            $table->dateTime('transfer_date');
            $table->smallInteger('work_time');
            $table->char('alpha',2);
            $table->smallInteger('to_self');
            $table->smallInteger('to_root');
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
        Schema::dropIfExists('tc_germin_transfers');
    }
}
