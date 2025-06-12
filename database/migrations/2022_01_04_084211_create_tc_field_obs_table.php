<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcFieldObsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_field_obs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_worker_id')->nullable();
            $table->bigInteger('tc_field_id');
            $table->smallInteger('total_death')->default(0);
            $table->smallInteger('normal')->default(0);
            $table->smallInteger('abnormal')->default(0);
            $table->smallInteger('load')->default(0);
            $table->dateTime('ob_date')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('tc_field_obs');
    }
}
