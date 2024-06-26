<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateV2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates_v2', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->decimal('amount');
            $table->string('icon');
            $table->unsignedInteger('service_type_id');
            $table->unsignedInteger('station_id');
            $table->enum('rate_type', ['fixed', 'flexible', 'free'])->default('fixed');
            $table->boolean('is_postpaid');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_type_id')->references('id')->on('service_types');
            $table->foreign('station_id')->references('id')->on('stations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rate_v2');
    }
}
