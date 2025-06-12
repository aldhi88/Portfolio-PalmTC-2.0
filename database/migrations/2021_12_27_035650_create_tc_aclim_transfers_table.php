<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcAclimTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_aclim_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_aclim_ob_id');
            $table->bigInteger('tc_worker_id');
            $table->dateTime('transfer_date');
            $table->smallInteger('to_self');
            $table->smallInteger('to_next');
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
        Schema::dropIfExists('tc_aclim_transfers');
    }
}
