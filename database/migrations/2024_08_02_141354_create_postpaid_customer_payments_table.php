<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostpaidCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postpaid_customer_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('postpaid_customers');
            $table->decimal('amount_paid', 8, 2);
            $table->float('discount', 4, 2)->default(0.00);
            $table->float('witholding_tax', 4, 2)->default(0.00);
            $table->decimal('gross_expected_amount', 8, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('net_expected_amount', 8, 2);
            $table->decimal('balance', 8, 2)->default(0.00);
            $table->date('date');
            $table->time('time');
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
        Schema::dropIfExists('postpaid_customer_payments');
    }
}
