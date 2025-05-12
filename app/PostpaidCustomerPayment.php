<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostpaidCustomerPayment extends Model
{
    protected $guarded = ['id'];

    //
    public static function SavePayment($dateRange, $rateTitle, $amount, $withholding_tax, $discount) {

        PostpaidCustomerPayment::create([
            'customer_id' => $rateTitle,
            'amount_paid' => $amount,
            'discount' => $discount,
            'witholding_tax' => $withholding_tax,
            'gross_expected_amount' => 0,
            'start_date' => $dateRange->from,
            'end_date' => $dateRange->to,
            'net_expected_amount' => 0,
            'date' => date("Y-m-d"),
            'time' => date("H:i:s")
        ]);
    }

    public static function getPaymentHistory($dateRange) {
        $payments = PostpaidCustomerPayment::where('date', '>=', $dateRange->from)
            ->where('date', '<=', $dateRange->to)
            ->orderBy('date', 'desc')
            ->get();

        return $payments;
    }
}
