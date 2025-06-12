<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_samples', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sample_number');
            $table->bigInteger('master_treefile_id');
            $table->bigInteger('resample')->nullable();
            $table->text('program')->nullable();
            $table->text('desc')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('tc_samples');
    }
}
