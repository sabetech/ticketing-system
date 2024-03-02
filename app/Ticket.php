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
        $bulkTicketInfo = [];
        foreach($tickets as $ticket) {
           $bulkTicketInfo[] = [
            "title" => $ticket->ticketUUID,
            "rate_title" => $ticket->ticketRateID,
            "car_number" => $ticket->carNumber,
            "station_name" => $ticket->stationId,
            "issued_date_time" => $ticket->issuedDateTime,
            "agent_name" => $ticket->agent_id,
           ];
        }

        $idsSaved = Ticket::insertGetId($bulkTicketInfo);
        return $idsSaved;
    }

}
