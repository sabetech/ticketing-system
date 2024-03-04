<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;

class Ticket extends Model
{
    //
    protected $table = 'toll_tickets';

    public static function saveTicket($agent, $ticketUUID, $ticketRateID, $carNumber, $issuedDateTime, $deviceID) {

        if (Ticket::where('title', $ticketUUID)->exists()) return false;

        $ticket = new Ticket;
        $ticket->title = $ticketUUID;
        $ticket->rate_title = $ticketRateID;
        $ticket->car_number = $carNumber;
        $ticket->station_name = $agent->station()->id;
        $ticket->issued_date_time = $issuedDateTime;
        $ticket->agent_name = $agent->id;

        $rate = Rate::find($ticketRateID);
        if (!$rate) return false;
        $ticket->amount = $rate->amount;

        $ticket->device_id = $deviceID;

        Log::info($ticket);

        // $ticket->save();
        return $ticket;

    }

    public static function bulkSaveTicket($tickets) {

        $chunkedTickets = array_chunk($tickets, 500, true);

        foreach($chunkedTickets as $chunkedTicket) {

            Ticket::insert($chunkedTicket);

        }

    }

}
