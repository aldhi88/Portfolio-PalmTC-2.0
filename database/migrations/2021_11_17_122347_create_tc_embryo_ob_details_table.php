<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcEmbryoObDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_embryo_ob_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_embryo_ob_id');
            $table->bigInteger('tc_embryo_bottle_id');
            $table->bigInteger('tc_worker_id');
            $table->smallInteger('bottle_embryo')->default(0);
            $table->smallInteger('bottle_oxidate')->default(0);
            $table->smallInteger('bottle_contam')->default(0);
            $table->smallInteger('bottle_other')->default(0);
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
        Schema::dropIfExists('tc_embryo_ob_details');
    }
}
