<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcNursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_nurs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_nur_transfer_id')->nullable();
            $table->bigInteger('tc_harden_transfer_id')->nullable();
            $table->bigInteger('tc_worker_id');
            $table->tinyInteger('category')->default(1);
            $table->string('block')->nullable();
            $table->string('row')->nullable();
            $table->string('tree')->nullable();
            $table->bigInteger('tc_plantation_id')->nullable();
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
        Schema::dropIfExists('tc_nurs');
    }
}
