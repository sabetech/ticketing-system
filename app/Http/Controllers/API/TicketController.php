<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Agent;
use App\Ticket;
use App\Rate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class TicketController extends BaseController {

    public function index() {
        //get all tickets here ...

    }

    public function getAgentTickets($id, Request $request) {

        $agent = Agent::find($id);
        if (!$agent) {
            return $this->sendError("Agent not found");
        }

        $date = $request->get('date');

        if (!$date) {
            $date = date("Y-m-d");
        }

        $tickets = $agent->tickets()->whereDate('issued_date_time', $date)->get();

        return $this->sendResponse($tickets, "Tickets retrieved successfully");

    }

    public function getAgentTicketsCount($id, Request $request) {

        $agent = Agent::find($id);
        $date = $request->get('date');
        if (!$agent) {
            return $this->sendError("Agent not found");
        }

        if (!$date) {
            $date = date("Y-m-d");
        }

        $count = Ticket::where('agent_name', $agent->id)
                        ->whereDate('issued_date_time', $date)->count();

    }

    public function postTicketSubmit($id, Request $request) {
        $agent = Agent::find($id);

        if (!$agent) return $this->sendError("Agent not found");

        $ticketClientId = $request->get('ticket_client_id');
        $ticketUUID = $request->get('ticket_uuid');
        $ticketRateID = $request->get('rate_id');
        $carNumber = $request->get('car_number');
        $issuedDateTime = $request->get('issued_date_time');
        $deviceID = $request->get('device_id');

        $saved = Ticket::saveTicket($agent, $ticketUUID, $ticketRateID, $carNumber, $issuedDateTime, $deviceID);

        if (!$saved) {
            return $this->sendError("Could Not save Ticket");
        }

        return $this->sendResponse([
            'ticket_client_id' => $ticketClientId,
        ] , "Ticket Saved Successfully");
    }

    public function getTicketParams($ticketInfo) {}

    public function postSyncTicket(Request $request) {

        $tickets = $request->get('tickets-log');

        Log::info("TICKET LOG::", $tickets);
        //handle agent id not found..

        $bulkTicketSave = [];
        foreach($tickets as $ticket) {
            $myTicket = new Ticket;
            $myTicket->title = $ticket['ticket_uuid'];
            $myTicket->rate_title = $ticket['rate_id'];
            $myTicket->car_number = $ticket['car_number'];
            $myTicket->issued_date_time = $ticket['issued_date_time'];
            $myTicket->agent_name = $ticket['agent_id'];
            $rate = Rate::find($ticket['rate_id']);

            if (!$rate) return false;
            $myTicket->amount = $rate->amount;

            $myTicket->device_id = $ticket['device_id'];

            $bulkTicketSave[] = $myTicket;
        }

        Log::info("BULK TICKETS::", $bulkTicketSave);
        // Ticket::bulkSaveTicket($tickets);

    }
}
