<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Agent;

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

}
