<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    //
    protected $table = 'toll_tickets';

    public function saveTicket($ticketInfo) {
        /*
        $ticketInfo->uuid
        $ticketInfo->rateId
        $ticketInfo->car_number
        $ticket

        */
    }

}
