<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Rate;
use App\Agent;
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

    public function listRates(Request $request){
        $input = $request->get('station', null);

        $rates = Rate::getAllRates($input);
        return $this->sendResponse($rates, 'Rates fetched successfully');

    }

    public function create(Request $request) {

        Log::info("REQUEST TO CREATE::", $request->all());

        $title = $request->get('title');
        $amount = $request->get('amount');
        $stationId = $request->get('station');
        $rate_type = $request->get('rate_type');
        $is_postpaid = $request->get('is_postpaid');

        $file = $request->file('rate_image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = Storage::disk('local')->put($filename, file_get_contents($file));

        $rate = new Rate();
        $rate->title = $title;
        $rate->amount = $amount;
        $rate->station_id = $stationId;
        $rate->is_postpaid = $is_postpaid;
        $rate->rate_type = $rate_type;
        $rate->service_type_id = 1;
        $rate->icon = $path;

        $rate->save();

        return $this->sendResponse($rate, 'Rate created successfully');

     }
}
