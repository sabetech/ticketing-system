<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Agent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class TicketController extends BaseController {

    public function index() {
        //get all tickets here ...

    }

    public function getAgentTickets(Agent $agent, Request $request) {

        Log::info($agent);

        $tickets = $agent->tickets;

        Log::info($tickets);

        return $this->sendResponse($tickets, "Tickets retrieved successfully");

    }

}
