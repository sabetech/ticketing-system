<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPostpaidCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postpaid_customer_payments', function (Blueprint $table) {
            $table->renameColumn('start_date', 'start_date_time');
            $table->renameColumn('end_date', 'end_date_time');
            $table->dateTime('start_date_time')->change();
            $table->dateTime('end_date_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postpaid_customer_payments', function (Blueprint $table) {
            $table->renameColumn('start_date_time', 'start_date');
            $table->renameColumn('end_date_time', 'end_date');
            $table->date('start_date')->change();
            $table->date('end_date')->change();
        });
    }
}