<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AgentOnlineStatus;

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
        return $this->station()->rates;
    }

    public function agentOnlineStatus() {
        return $this->hasOne("App\AgentOnlineStatus", "agent_id", "id");
    }

    public function setLoginTimeStamp() {
        $onlineStatus = $this->agentOnlineStatus;
        if (!$onlineStatus) {
            $onlineStatus = new AgentOnlineStatus();
            $onlineStatus->agent_id = $this->id;
            $onlineStatus->loggedin_at = date("Y-m-d H:i:s");
            $onlineStatus->latest_online_at = date("Y-m-d H:i:s");
            $onlineStatus->device_id = "Logged In";
            $onlineStatus->save();
        }
    }

    public function updateOnlineStatus() {

        $onlineStatus = $this->agentOnlineStatus;
        if ($onlineStatus) {
            $onlineStatus->latest_online_at = date("Y-m-d H:i:s");
            $onlineStatus->save();
        }

    }
}
