<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Agent;
use App\AgentOnlineStatus;
use Carbon\Carbon;
use stdClass;

class AgentController extends BaseController {

    public function ping($id, Request $request){
        $agent = Agent::find($id);

        if(!$agent) {
            return $this->sendError('Agent not found.');
        }

        $success = $agent->updateOnlineStatus();
        if ($success)
            return $this->sendResponse('pong', 'Agent is online');
        return $this->sendError('Couldn\'t update Agent online status');
    }

    public function agentCount(Request $request){

        $date = $request->get('date');

        $agentCount = Agent::getAgentCountByDate($date);

        return $this->sendResponse($agentCount , "Successfully Got The Agent Count For Given Date");

    }

    public function agentOnlineStatus(Request $request) {

        $agentOnlineStatus = AgentOnlineStatus::with('agent')->get();



        return $this->sendResponse($agentOnlineStatus, "Agent Statuses fetched successfully");
    }

    public function getAllAgents() {
        $agents = Agent::all();

        foreach ($agents as &$agent) {
            $agent->stationInfo = $agent->station();
        }

        return $this->sendResponse($agents, "Agents Fetched successfully");
    }

    public function show($id, Request $request) {
        $agent = Agent::find($id);

        if (!$agent) {
            return $this->sendError('Agent not found.');
        }

        $agentTicketInfo = new stdClass;
        $agentTicketInfo->agent = $agent;
        $agent->stationUser->station;

        $from = $request->get('from', null);
        $to   = $request->get('to', null);

        if (!$from) {
            $todayStart = Carbon::today()->startOfDay();
            $from = $todayStart->format('Y-m-d H:i:s');
        }

        if (!$to) {
            $todayEnd = Carbon::today()->endOfDay();
            $to = $todayEnd->format('Y-m-d H:i:s');
        }

        $agentTicketInfo->tickets = $agent->getAgentTickets($from, $to);

        return $this->sendResponse($agentTicketInfo, "Agent Tickets fetched successfully");

    }

}
