<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Agent;
use App\AgentOnlineStatus;

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
        $agents = Agent::with('tickets')->get();

        foreach ($agents as $agent) {
            $agent->stationInfo = $agent->station();
            $agent->totalTickets = $agent->tickets->count();
        }


        return $this->sendResponse($agents, "Agents Fetched successfully");
    }

}
