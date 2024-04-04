<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Rate;
use App\Agent;
use App\Http\Controllers\API\BaseController as BaseController;

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

    public function listRates(Request $request){
        $input = $request->get('station', null);

        $rates = Rate::getAllRates($input);
        return $this->sendResponse($rates, 'Rates fetched successfully');

    }
}
