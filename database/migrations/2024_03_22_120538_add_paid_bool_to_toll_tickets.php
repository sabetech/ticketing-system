<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidBoolToTollTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('toll_tickets', function (Blueprint $table) {
            //
            $table->boolean('paid')->default(true)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('toll_tickets', function (Blueprint $table) {
            //
            $table->dropColumn('paid');
        });
    }
}
