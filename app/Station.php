<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    //
    protected $table = 'stations';

    public function agents() {
        return $this->hasManyThrough('\App\Agent', '\App\StationUser');
    }

    public function rates() {
        return $this->hasMany('\App\Rate', 'station_id', 'id');
    }
}
