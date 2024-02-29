<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Rate;
use App\Http\Controllers\API\BaseController as BaseController;

class RateController extends BaseController
{
    //
    public function index(){

        $rates = Rate::all();

        return $this->sendResponse($rates, "Rates fetched successfully");
    }
}
