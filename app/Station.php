<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    //
    protected $table = 'stations';

    public function agents() {
        return $this->hasMany('\App\Agent', 'stations_user', 'stations.id', 'users.id');
    }

    public function rates() {

        return $this->hasMany('\App\Rate', 'station_id', 'id');
    }
}
