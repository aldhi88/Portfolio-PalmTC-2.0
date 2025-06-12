<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcInitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_inits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_sample_id');
            $table->bigInteger('tc_room_id');
            $table->integer('number_of_block');
            $table->integer('number_of_bottle');
            $table->integer('number_of_plant');
            $table->text('desc')->nullable();
            $table->dateTime('date_work');
            $table->dateTime('date_stop')->nullable();
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
        Schema::dropIfExists('tc_inits');
    }
}
