<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcGerminObsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_germin_obs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_worker_id')->nullable();
            $table->char('alpha',2)->nullable();
            $table->smallInteger('total_bottle_germin')->default(0);
            $table->smallInteger('total_bottle_oxidate')->default(0);
            $table->smallInteger('total_bottle_contam')->default(0);
            $table->smallInteger('total_bottle_other')->default(0);
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
        Schema::dropIfExists('tc_germin_obs');
    }
}
