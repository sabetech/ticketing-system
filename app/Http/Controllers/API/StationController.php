<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Station;
use DB;

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

        // Single optimized query
        $results = DB::table('toll_tickets')
        ->join('stations', 'toll_tickets.station_name', '=', 'stations.id')
        ->join('rates_v2', 'toll_tickets.rate_title', '=', 'rates_v2.id')
        ->whereBetween('toll_tickets.issued_date_time', [$from, $to])
        ->select('stations.id as station_id', 'stations.name','rates_v2.id as rate_id','rates_v2.title', 'rates_v2.icon',DB::raw('COUNT(*) as ticket_count'),DB::raw('SUM(toll_tickets.amount)as total_amount'))
        ->groupBy('stations.id', 'stations.name', 'rates_v2.id', 'rates_v2.title', 'rates_v2.icon')
        ->orderBy('stations.name')
        ->orderBy('rates_v2.title')
        ->get();

    return $this->sendResponse($results, 'Stations Summary retrieved successfully.');
    }
}
