<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntryTypeFieldToRatesV2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rates_v2', function (Blueprint $table) {
            //
            $table->enum('entry_identifier', ['car_number', 'phone_number'])->default('car_number')->after('is_postpaid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rates_v2', function (Blueprint $table) {
            //
            $table->dropColumn('entry_identifier');
        });
    }
}
