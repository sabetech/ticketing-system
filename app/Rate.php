<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    //
    protected $table = "rates_v2";

    public function station() {
        return $this->belongsTo('App\Station', 'station_id', 'id');
    }

    public static function getAllRates($station = null) {
        if ($station == null)
            return Rate::with('station')->where('service_type_id', 1)->get();

        return Rate::with('station')
            ->where('station_id', $station)
            ->where('service_type_id', 1)->get();
    }
}
