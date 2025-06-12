<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcFieldObDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_field_ob_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_field_tree_id');
            $table->bigInteger('tc_field_ob_id');
            $table->tinyInteger('is_death')->default(0);
            $table->bigInteger('tc_death_id')->nullable();
            $table->tinyInteger('is_normal')->default(0);
            $table->smallInteger('load')->default(0)->nullable();
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
        Schema::dropIfExists('tc_field_ob_details');
    }
}
