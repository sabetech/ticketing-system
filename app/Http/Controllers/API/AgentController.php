<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Agent;
use App\AgentOnlineStatus;
use App\Station;
use App\StationUser;
use Carbon\Carbon;
use stdClass;
use Illuminate\Support\Facades\Log;

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
        $agents = Agent::filterByRole('agent');

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
        if ($agent->stationUser) {
            $agent->stationUser->station;
        }

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

    public function updateStation($id, Request $request) {
        $me = Auth::guard('api')->user();

        Log::info("Authenticated user: " . ($me ? $me->id : 'null'));

        $myRole = $me && $me->roles && $me->roles->count() > 0
            ? strtolower($me->roles[0]->name)
            : null;

        Log::info("Authenticated user role: " . ($myRole ?? 'null'));

        if (!in_array($myRole, ['administrator', 'supervisor'])) {
            return $this->sendError('Unauthorized. Only Admin or Supervisor can change an agent station.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'station_id' => 'required|integer|exists:stations,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $agent = Agent::find($id);

        Log::info($agent);

        if (!$agent) {
            return $this->sendError('Agent not found.');
        }

        $isAgent = $agent->roles->contains(function ($role) {
            return strtolower($role->name) === 'agent';
        });

        if (!$isAgent) {
            return $this->sendError('User is not an agent.');
        }

        DB::transaction(function () use ($agent, $request) {
        StationUser::where('user_id', $agent->id)->delete();

        StationUser::create([
                'user_id' => $agent->id,
                'stations_id' => $request->get('station_id'),
            ]);
        });

        $station = Station::find($request->get('station_id'));

        return $this->sendResponse($station, 'Agent station updated successfully.');
    }

}
