<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcEmbryoListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_embryo_lists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_embryo_bottle_id');
            $table->bigInteger('tc_worker_id');
            $table->bigInteger('tc_embryo_ob_id')->nullable();
            $table->bigInteger('tc_embryo_transfer_id')->nullable();
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
        Schema::dropIfExists('tc_embryo_lists');
    }
}
