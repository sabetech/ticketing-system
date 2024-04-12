<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;

class Ticket extends Model
{
    //
    protected $table = 'toll_tickets';

    public function rate(){
        return $this->belongsTo('App\Rate', 'rate_title', 'id');
    }

    public function agent(){
        return $this->belongsTo('\App\Agent', 'agent_name', 'id');
    }

    public static function saveTicket($ticket) {

        if (Ticket::where('title', $ticket->title)->exists()) return false; //it means ticket has already been saved

        $rate = Rate::find($ticket->rate_title);
        if (!$rate) return false;
        $ticket->amount = $rate->amount;

        Log::info($ticket);

        // $ticket->save();
        return $ticket;

    }

    public static function saveTraderPayment($ticket, $amount) {
        if (Ticket::where('title', $ticket->title)->exists()) return false; //it means ticket has already been saved

        $rate = Rate::find($ticket->rate_title);
        if (!$rate) return false;

        $ticket->amount = $amount;


        Log::info($ticket);

        // $ticket->save();

        return $ticket;
    }

    public static function bulkSaveTicket($tickets) {

        $chunkedTickets = array_chunk($tickets, 500, true);

        foreach($chunkedTickets as $chunkedTicket) {

            Ticket::insert($chunkedTicket);

        }
        return true;
    }

    public static function getTickets($date) {

        return self::with('rates_v2')->where('issued_date_time', '>=', $date)->get();

    }

    public static function getTicketsFromRange($startDate, $endDate) {

        $tickets = self::join('stations', 'stations.id', '=', 'toll_tickets.station_name')->with(['rate', 'agent'])->whereBetween('issued_date_time', [$startDate, $endDate])->get();

        return $tickets;

    }

    public static function getTicketCount($date) {
        return self::where('issued_date_time', '>=', $date)->count();
    }

    public static function calculateRevenue($date) {

        $amountSum = self::where('issued_date_time', '>=', $date)
                        ->where('paid', 1)->sum('amount');

        return $amountSum;
    }

    public static function calculateUnpaidTickets($date) {
        $unpaidAmount = self::where('issued_date_time', '>=', $date)
                        ->where('paid', 0)->sum('amount');

        return $unpaidAmount;
    }

    public static function countUnpaidTickets($date) {
        $unpaidTickets = self::where('issued_date_time', '>=', $date)
                    ->where('paid', 0)->count();

        return $unpaidAmount;
    }

    public static function getThirdPartyTickets($from, $to) {

        $thirdPartyTickets = self::join('rates_v2', 'toll_tickets.rate_title', '=', 'rates_v2.id')->whereBetween('issued_date_time', [$from, $to])
            ->where('rates_v2.is_postpaid', 1)->get();

        return $thirdPartyTickets;
    }

}
