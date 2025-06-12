<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcEmbryoBottleSubtractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_embryo_bottle_subtractions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_embryo_bottle_id');
            $table->integer('bottle_count');
            $table->text('reason');
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
        Schema::dropIfExists('tc_embryo_bottle_subtractions');
    }
}
