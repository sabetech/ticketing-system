<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostpaidCustomerPayment extends Model
{
    //
    public static function SavePayment($dateRange, $rateTitle, $amount, $withholding_tax, $discount) {
        $from = $dateRange[0];
        $to = $dateRange[1];
        PostpaidCustomerPayment::create([
            'from' => $from,
            'to' => $to,
        ]);
    }
}
