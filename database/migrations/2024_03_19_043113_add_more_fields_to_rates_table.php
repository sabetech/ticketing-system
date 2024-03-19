<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rates', function (Blueprint $table) {
            //
            $table->boolean('on_credit')->after('station_id')->default(false)->comment('field for whether their money is paid at the end of the month');
            $table->enum('rate_type', ['vehicle', 'trader'])->after('on_credit')->default('vehicle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rates', function (Blueprint $table) {
            //
            $table->dropColumn('on_credit');
            $table->dropColumn('rate_type');
        });
    }
}
