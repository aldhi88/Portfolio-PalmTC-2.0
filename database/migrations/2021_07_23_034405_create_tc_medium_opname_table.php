<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcMediumOpnameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_medium_opname', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_medium_stock_id');
            $table->integer('stock_in')->default(0);
            $table->integer('stock_out')->default(0);
            $table->text('desc')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tc_medium_opname');
    }
}
