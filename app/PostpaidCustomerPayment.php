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

        $tickets = Ticket::getThirdPartyTickets($dateRange->from, $dateRange->to);

        //get amount to be paid the $tickets
        $ticketsTotal = 0;
        foreach ($tickets as $ticket) {
            $ticketsTotal += floatval($ticket->amount);
        }

        $netExpectedAmount = $ticketsTotal - ($ticketsTotal * $discount) - ($ticketsTotal * $withholding_tax);

        PostpaidCustomerPayment::create([
            'customer_id' => $rateTitle,
            'amount_paid' => $amount,
            'discount' => $discount,
            'witholding_tax' => $withholding_tax,
            'gross_expected_amount' => $ticketsTotal,
            'start_date_time' => $dateRange->from,
            'end_date_time' => $dateRange->to,
            'net_expected_amount' => $netExpectedAmount,
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
