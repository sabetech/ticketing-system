<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostpaidCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postpaid_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('station_id');
            $table->unsignedInteger('rate_id');
            $table->foreign('station_id')->references('id')->on('stations');
            $table->foreign('rate_id')->references('id')->on('rates');
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
        Schema::dropIfExists('postpaid_customers');
    }
}
