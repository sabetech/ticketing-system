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

        // Single optimized query
        $results = DB::table('toll_tickets')
        ->join('rates_v2', 'toll_tickets.rate_title', '=', 'rates_v2.id')
        ->join('stations', 'rates_v2.station_id', '=', 'stations.id')
        ->whereBetween('toll_tickets.issued_date_time', [$from, $to])
        ->whereNull('toll_tickets.deleted_at')
        ->select('stations.id as station_id', 'stations.name','rates_v2.id as rate_id','rates_v2.title', 'rates_v2.icon', 'rates_v2.rate_type', 'rates_v2.is_postpaid', DB::raw('COUNT(*) as ticket_count'),DB::raw('SUM(toll_tickets.amount) as total_amount'))
        ->groupBy('stations.id', 'stations.name', 'rates_v2.id', 'rates_v2.title', 'rates_v2.icon', 'rates_v2.rate_type', 'rates_v2.is_postpaid')
        ->orderBy('stations.name')
        ->orderBy('rates_v2.title')
        ->get();

    return $this->sendResponse($results, 'Stations Summary retrieved successfully.');
    }
}
