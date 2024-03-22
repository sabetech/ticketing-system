<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgentOnlineStatus extends Model
{
    //
    protected $table = 'agent_online_status';

    public function agent(){
        return  $this->belongsTo('App\Agent','agent_id', 'id');
    }

}
