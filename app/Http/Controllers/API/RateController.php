<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Rate;
use App\AgentRate;
use App\Agent;
use App\Ticket;
use App\Http\Controllers\API\BaseController as BaseController;
use App\PostpaidCustomerPayment;
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
        $rateId =  $request->get('rateId');
        if (!$agent) return $this->sendError("Agent not found");

        Log::info($rateId);

        if (AgentRate::where('agent_id', $agent->id)->where('rate_id', $rateId)->delete()) {
            return $this->sendResponse($rateId, "Agent Rate removed Successfully");
        }

        AgentRate::create([
            'agent_id' => $agent->id,
            'rate_id' => $rateId
        ]);

        return $this->sendResponse($rateId, "Agent Rates Added Successfully");

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

        $filename = $request->icon->store('public/img/rates');

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
        if ($request->hasFile('icon')) {
            $filename = $request->icon->store('public/img/rates');
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
        $withholding_tax = $request->get('tax',0);
        $discount = $request->get('discount',0);
        $rateTitle = $request->get('client_id');

        Log::info($request->all());
        $withholding_tax = floatval($withholding_tax) / 100;
        $discount = floatval($discount) / 100;

        PostpaidCustomerPayment::SavePayment($date_range, $rateTitle, $amount, $withholding_tax, $discount);
        $result = Ticket::makePayment($date_range, $rateTitle);

        //for this date range, get all the unpaid tickets and set to paid=true
        Log::info($result);

        return $this->sendResponse($result, 'Tickets Paid successfully');
    }
}
