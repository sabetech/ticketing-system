<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Station;

class StationController extends BaseController
{
    //
    public function getAllStations() {
        $stations = Station::all();

        return $this->sendResponse($stations, 'Stations retrieved successfully.');
    }

    public function getStationsSummary(Request $request) {
        $from = $request->get('from');
        $to = $request->get('to');

        $stationData = [];
        $stations = Station::all();

        foreach($stations as $station) {
            $stationData[$station->name] = $station->tickets()->whereBetween('issued_date_time', [$from, $to])->get();
        }

        return $this->sendResponse($stationData, 'Stations retrieved successfully.');
    }
}
