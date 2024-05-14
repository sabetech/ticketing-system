<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AgentOnlineStatus;
use App\Ticket;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;
    //
    protected $table = 'users';


    public function station(){
        if (isset($this->stationUser))
            return $this->stationUser->station;
        return null;
    }

    public function stationUser() {
        return $this->hasOne("App\StationUser", "user_id", "id");
    }

    public function tickets(){
        return $this->hasMany("\App\Ticket", "agent_name", "id");
    }

    public function getRates() {
        $rates = $this->station()->rates;



        //check junction table and filter ...
        return $rates;

    }

    public function agentOnlineStatus() {
        return $this->hasOne("App\AgentOnlineStatus", "agent_id", "id");
    }

    public static function getAgentCountByDate($date) {
        $distinctValues = Ticket::where('issued_date_time', '>=', $date)
        ->select(DB::raw('COUNT(DISTINCT agent_name) AS agentCount'))
        ->first();

        return $distinctValues->agentCount;
    }

    public function setLoginTimeStamp() {
        $onlineStatus = $this->agentOnlineStatus;
        if (!$onlineStatus) {
            $onlineStatus = new AgentOnlineStatus();
            $onlineStatus->agent_id = $this->id;
            $onlineStatus->loggedin_at = date("Y-m-d H:i:s");
            $onlineStatus->latest_online_at = date("Y-m-d H:i:s");
            $onlineStatus->save();
        }else {
            $onlineStatus->latest_online_at = date("Y-m-d H:i:s");
            $onlineStatus->loggedin_at = date("Y-m-d H:i:s");
            $onlineStatus->loggedout_at = null;
            $onlineStatus->save();
        }
    }

    public function updateOnlineStatus() {

        $onlineStatus = $this->agentOnlineStatus;
        if ($onlineStatus) {
            $onlineStatus->latest_online_at = date("Y-m-d H:i:s");
            $onlineStatus->save();
            return true;
        }
        return false;
    }

    public function setLogoutTimestamp(){
        $onlineStatus = $this->agentOnlineStatus;
        if ($onlineStatus) {
            $onlineStatus->loggedout_at = date("Y-m-d H:i:s");
            $onlineStatus->save();
        }
    }

    public function getAgentTickets($from, $to) {
        return $this->tickets()->with(['rate' => function ($query) {
            $query->select('title', 'icon', 'station_id', 'rate_type', 'is_postpaid');
        }])->whereBetween('issued_date_time', [$from, $to])->orderBy('issued_date_time', 'desc')
        ->select('agent_name', 'toll_tickets.amount', 'car_number', 'toll_tickets.id as id', 'issued_date_time', 'device_id', 'paid')
        ->get();
    }

    public function getAgentAggregate($from, $to) {
        return $this->tickets()->whereBetween('issued_date_time', [$from, $to])->get();
    }
}
