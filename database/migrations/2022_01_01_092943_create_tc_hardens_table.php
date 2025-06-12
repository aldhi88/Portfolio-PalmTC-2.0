<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcHardensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_hardens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_harden_transfer_id')->nullable();
            $table->bigInteger('tc_aclim_transfer_id')->nullable();
            $table->bigInteger('tc_worker_id');
            $table->tinyInteger('sub');
            $table->string('type');
            $table->string('alpha',3);
            $table->dateTime('tree_date');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('tc_hardens');
    }
}
