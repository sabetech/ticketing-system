<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostpaidCustomerPayment extends Model
{
    protected $guarded = ['id'];

    public function customer() {
        return $this->belongsTo('App\Rate', 'customer_id', 'id');
    }
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

    public static function getPaymentHistory($from, $to) {
        $payments = PostpaidCustomerPayment::with('customer')->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date', 'desc')
            ->get();

        return $payments;
    }
}
