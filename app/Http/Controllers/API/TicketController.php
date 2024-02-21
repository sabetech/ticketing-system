<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Agent;
use Illuminate\Support\Facades\Auth;


class TicketController extends BaseController {

    public function index() {
        //get all tickets here ...

    }

    public function getAgentTickets(Agent $agent, Request $request) {

        $tickets = $agent->tickets;

        return $this->sendResponse($tickets, "Tickets retrieved successfully");

    }

}
