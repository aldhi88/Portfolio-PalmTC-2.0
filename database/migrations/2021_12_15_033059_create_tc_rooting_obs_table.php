<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcRootingObsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_rooting_obs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_worker_id')->nullable();
            $table->string('alpha',2)->nullable();
            $table->smallInteger('total_bottle_rooting')->default(0);
            $table->smallInteger('total_leaf_rooting')->default(0);
            $table->smallInteger('total_bottle_oxidate')->default(0);
            $table->smallInteger('total_leaf_oxidate')->default(0);
            $table->smallInteger('total_bottle_contam')->default(0);
            $table->smallInteger('total_leaf_contam')->default(0);
            $table->smallInteger('total_bottle_other')->default(0);
            $table->smallInteger('total_leaf_other')->default(0);
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
        Schema::dropIfExists('tc_rooting_obs');
    }
}
