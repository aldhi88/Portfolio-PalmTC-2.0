<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcCallusObDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_callus_ob_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_callus_ob_id');
            $table->bigInteger('tc_init_bottle_id');
            $table->tinyInteger('explant_number');
            $table->tinyInteger('result');
            $table->bigInteger('tc_contamination_id')->nullable();
            $table->boolean('is_count_bottle')->default(1);
            $table->boolean('is_count_explant')->default(1);
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
        Schema::dropIfExists('tc_callus_ob_details');
    }
}
