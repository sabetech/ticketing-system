<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    //
    protected $table = 'users';

    public function tickets(){
        return $this->hasMany("\App\Ticket", "agent_name", "id");
    }


}
