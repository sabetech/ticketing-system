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

        $ticketUUID = $request->get('ticket_uuid');
        $ticketRateID = $request->get('rate_id');
        $carNumber = $request->get('car_number');
        $issuedDateTime = $request->get('issued_date_time');
        $deviceID = $request->get('device_id');

        $ticket = new Ticket;
        $ticket->title = $ticketUUID;
        $ticket->rate_title = $ticketRateID;
        $ticket->car_number = $carNumber;
        $ticket->station_name = $agent->station()->id;
        $ticket->issued_date_time = $issuedDateTime;
        $ticket->agent_name = $agent->id;
        $ticket->device_id = $deviceID;

        $rateType = $request->get('rate_type');
        $savedTicket = null;

        switch ($rateType) {
            case 'vehicle':
                $savedTicket = Ticket::saveTicket($ticket);
                break;
            case 'trader':
                $amount = $request->get('amount');
                $savedTicket = Ticket::saveTraderPayment($ticket, $amount);
                break;
            default:
                $savedTicket = Ticket::saveTicket($ticket);


        }

        if (!$savedTicket) {
            return $this->sendError("Could Not save Ticket Or Ticket already saved");
        }

        return $this->sendResponse([
            'ticket_uuid' => $savedTicket->title,
        ] , "Ticket Saved Successfully");
    }

    public function getTicketByDate(Request $request) {
        $date = $request->get('date');

        $tickets = Ticket::getTickets($date);

        return $this->sendResponse($tickets , "Successfully Got All Tickets For Given Date");
    }

    public function getTicketByDateRange(Request $request) {
        $dateRange = $request->get('date_range');
        Log::info($dateRange);
        list($startDate, $endDate) = explode(',',$dateRange);

        $tickets = Ticket::getTicketsFromRange($startDate, $endDate);

        return $this->sendResponse($tickets, "Successfully Fetched tickets from date range");

    }

    public function getTicketCountByDate(Request $request) {
        $date = $request->get('date');

        $count = Ticket::getTicketCount($date);

        return $this->sendResponse($count , "Successfully Got The Ticket Count For Given Date");

    }

    public function calculateTicketRevenueByDate(Request $request) {
        $date = $request->get('date');

        $revenue = Ticket::calculateRevenue($date);

        return $this->sendResponse($revenue , "Successfully Got The Ticket Revenue For Given Date");
    }

    public function calculateUnpaidTickets(Request $request) {
        $date = $request->get('date');

        $unpaidAmount = Ticket::calculateUnpaidTickets($date);

        return $this->sendResponse($unpaidAmount , "Successfully Got The Unpaid Amount For Given Date");
    }

    public function countUnpaidTickets(Request $request) {
        $date = $request->get('date');

        $unpaidTickets = Ticket::countUnpaidTickets($date);

        return $this->sendResponse($unpaidTickets , "Successfully Got The Unpaid Tickets For Given Date");
    }

    public function getTop5(Request $request) {

    }

    public function postSyncTicket(Request $request) {

        $tickets = $request->get('tickets-log');

        Log::info("TICKET LOG::", $tickets);
        //handle agent id not found..

        $bulkTicketSave = [];
        $ticketTitles = [];
        foreach($tickets as $ticket) {
            $myTicket = [];
            $myTicket['title'] = $ticket['ticket_uuid'];
            $myTicket['rate_title'] = $ticket['rate_id'];
            $myTicket['car_number'] = $ticket['car_number'];
            $myTicket['issued_date_time'] = $ticket['issued_date_time'];
            $myTicket['agent_name'] = $ticket['agent_id'];
            $myTicket['created_at'] = date("Y-m-d H:i:s");
            $myTicket['updated_at'] = date("Y-m-d H:i:s");

            $agent = Agent::find($ticket['agent_id']);
            if (!$agent) continue;

            $station = $agent->station();
            $myTicket['station_name'] = $station->id;
            $rate = Rate::find($ticket['rate_id']);

            if (!$rate) continue;
            $myTicket['amount'] = $rate->amount;

            $myTicket['device_id'] = $ticket['device_id'];

            $bulkTicketSave[] = $myTicket;
            $ticketTitles[] = $myTicket['title'];
        }

        $existingTicketsTitles = Ticket::whereIn('title', $ticketTitles)->pluck('title');

       $bulkTicketSave = array_filter($bulkTicketSave, function ($ticketItem) use ($existingTicketsTitles){
            return !$existingTicketsTitles->contains($ticketItem['title']);
       });

        Log::info("\n\nBULK TICKETS::", $bulkTicketSave);

        if (count($bulkTicketSave) == 0)
            return $this->sendResponse($ticketTitles, "Tickets were synced already");

       Ticket::bulkSaveTicket($bulkTicketSave);

       return $this->sendResponse($ticketTitles, "Tickets were synced successfully");

    }
}
