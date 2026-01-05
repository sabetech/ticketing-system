<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexesOnTollTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toll_tickets', function (Blueprint $table) {
            $table->index('issued_date_time');
            $table->index('station_name');
            $table->index('rate_title');
        });
        Schema::table('stations', function (Blueprint $table) {
            // Covering index to avoid table lookups
            $table->index(['id', 'name']);
        });

        Schema::table('rates_v2', function (Blueprint $table) {
            // Covering index to avoid table lookups
            $table->index(['id', 'title', 'icon']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('indexes_on_toll_tickets');
    }
}
