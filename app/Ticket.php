<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;

class Ticket extends Model
{
    //
    protected $table = 'toll_tickets';

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

        if ($rate->rate_type === 'trader') {
            $ticket->amount = $amount;
        }

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

        return self::where('issued_date_time', '>=', $date)->get();

    }

}
