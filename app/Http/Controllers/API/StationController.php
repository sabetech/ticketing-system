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
            $stationData[$station->name] = $station->tickets()->whereBetween('issued_date_time', [$from, $to])->with('rate')->get();
        }

        return $this->sendResponse($stationData, 'Stations retrieved successfully.');
    }

    public function getStationsSummary_test(Request $request) {
        $from = $request->get('from');
        $to = $request->get('to');

        // Single optimized query with eager loading
        $stations = Station::with(['tickets' => function ($query) use ($from, $to) {
                $query->whereBetween('issued_date_time', [$from, $to])
                    ->with('rate')
                    ->select('id', 'station_name', 'issued_date_time', 'rate_id', 'rate.title', 'rate.icon'); // Select only needed columns
            }])
            ->get(['id', 'name']); // Select only needed columns

        // Transform to desired format
        $stationData = $stations->pluck('tickets', 'name')->toArray();

    return $this->sendResponse($stationData, 'Stations retrieved successfully.');
    }
}
