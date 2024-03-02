<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;

class Ticket extends Model
{
    //
    protected $table = 'toll_tickets';

    public static function saveTicket($agent, $ticketUUID, $ticketRateID, $carNumber, $issuedDateTime, $deviceID) {

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
       return true;

    }

    public function bulkTicketSave($tickets) {

        $idsSaved = Ticket::insertGetId($tickets);
        Log::info($idsSaved);

        return $idsSaved;
    }

}
