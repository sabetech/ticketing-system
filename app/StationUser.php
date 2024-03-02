<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StationUser extends Model
{
    //
    protected $table = 'stations_user';

    public function Agent(){
        return $this->hasOne("\App\Agent", "user_id", "id");
    }

    public function Station(){
        return $this->belongsTo("\App\Station", "station_id", "id");
    }


}
