<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rate extends Model
{
    use SoftDeletes;
    //
    protected $table = "rates_v2";
    protected $guarded = ['id'];

    public function station() {
        return $this->belongsTo('App\Station', 'station_id', 'id');
    }

    public static function getAllRates($station = null, $isPostpaid = false) {
        if ($station == null)
            return Rate::with('station')->where('service_type_id', 1)->get();

        if ($isPostpaid) {
            return Rate::with(['station',])
                ->where('station_id', $station)
                ->where('is_postpaid', 1)
                ->get();
        }

        return Rate::with('station')
            ->where('station_id', $station)
            ->where('service_type_id', 1)->get();
    }
}
