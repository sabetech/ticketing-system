<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Rate;
use App\AgentRate;
use App\Agent;
use App\Ticket;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Storage;
use Log;

class RateController extends BaseController
{
    //
    public function index(){

        $rates = Rate::all();

        return $this->sendResponse($rates, "Rates fetched successfully");
    }

    public function getRatesForAgent($id, Request $request) {

        $agent = Agent::find($id);
        if (!$agent) return $this->sendError("Agent not found");

        $rates = $agent->getRates();

        return $this->sendResponse($rates, "Rates fetched successfully");
    }

    public function updateRatesForAgent($id, Request $request) {
        $agent = Agent::find($id);
        $rateIds =  $request->get('rateIds');
        if (!$agent) return $this->sendError("Agent not found");

        Log::info($rateIds);
        //remove every instance of the agent id
        AgentRate::where('agent_id', $agent->id)->delete();

        $rateIds = explode(",", $rateIds);
        //
        foreach($rateIds as $rateId) {
            AgentRate::create([
                'agent_id' => $agent->id,
                'rate_id' => $rateId
            ]);
        }

        return $this->sendResponse([], "Agent Rates Configured Successfully");

    }

    public function listRates(Request $request){
        $input = $request->get('station', null);
        $isPostpaid = $request->get('postpaid', false);

        $rates = Rate::getAllRates($input, $isPostpaid);
        return $this->sendResponse($rates, 'Rates fetched successfully');

    }

    public function create(Request $request) {

        Log::info("REQUEST TO CREATE::", $request->all());

        $title = $request->get('title');
        $amount = $request->get('amount');
        $stationId = $request->get('station');
        $rate_type = $request->get('rate_type');
        $is_postpaid = $request->get('is_postpaid');

        $filename = $request->rate_image->store('public/img/rates');

        $rate = new Rate();
        $rate->title = $title;
        $rate->amount = $amount;
        $rate->station_id = $stationId;
        $rate->is_postpaid = $is_postpaid == "false" ? false : true;
        $rate->rate_type = $rate_type;
        $rate->service_type_id = 1;
        $rate->icon = $filename;

        $rate->save();

        return $this->sendResponse($rate, 'Rate created successfully');

     }

    public function delete($id, Request $request) {
        $rate = Rate::where('id', $id)->delete();

        return $this->sendResponse($rate, 'Rate has been deleted successfully');
    }

    public function edit($id, Request $request) {
        Log::info("REQUEST TO EDIT::", $request->all());

        $title = $request->get('title');
        $amount = $request->get('amount');
        $rate_type = $request->get('rate_type');
        $is_postpaid = $request->get('is_postpaid');
        $filename = '';
        if ($request->hasFile('rate_image')) {
            $filename = $request->rate_image->store('public/img/rates');
            $rate = Rate::where('id', $id)->update([
                'title' => $title,
                'amount' => $amount,
                'is_postpaid' => $is_postpaid == "false" ? false : true,
                'rate_type' => $rate_type,
                'service_type_id' => 1,
                'icon' => $filename
            ]);

            return $this->sendResponse($rate, 'Rate updated successfully');

        }

        $rate = Rate::where('id', $id)->update([
            'title' => $title,
            'amount' => $amount,
            'is_postpaid' => $is_postpaid == "false" ? false : true,
            'rate_type' => $rate_type,
            'service_type_id' => 1,
        ]);

        return $this->sendResponse($rate, 'Rate updated successfully');
    }

    public function makePayment(Request $request) {
        $dateRange = $request->get('dateRange');

        $date_range = json_decode($dateRange);

        $amount = $request->get('amount');
        $rateTitle = $request->get('client_id');

        Log::info($request->all());

        $numberOfTicketsPaidFor = Ticket::makePayment($date_range, $amount, $rateTitle);

        //for this date range, get all the unpaid tickets and set to paid=true
        Log::info($numberOfTicketsPaidFor);

        return $this->sendResponse(['number_of_tickets_paid' => $numberOfTicketsPaidFor], 'Tickets Paid successfully');
    }
}
