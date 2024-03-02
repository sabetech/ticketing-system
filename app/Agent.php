<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    //
    protected $table = 'users';

    public function station(){

        return $this->stationUser->station;
    }

    public function stationUser() {
        return $this->hasOne("App\StationUser", "user_id", "id");
    }

    public function tickets(){
        return $this->hasMany("\App\Ticket", "agent_name", "id");
    }

    public function getRates() {
        return $this->station->rates;
    }
}
