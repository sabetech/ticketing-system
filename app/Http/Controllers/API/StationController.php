<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Station;

class StationController extends Controller
{
    //
    public function getAllStations() {
        $stations = Station::all();

        return $this->sendResponse($stations, 'Stations retrieved successfully.');
    }
}
