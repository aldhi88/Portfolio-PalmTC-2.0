<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcNurObDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_nur_ob_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_nur_tree_id');
            $table->bigInteger('tc_nur_ob_id');
            $table->tinyInteger('is_death')->default(0);
            $table->bigInteger('tc_death_id')->nullable();
            $table->dateTime('pre_nursery')->nullable();
            $table->tinyInteger('is_transfer')->default(0);
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
        Schema::dropIfExists('tc_nur_ob_details');
    }
}
