<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgentRate extends Model
{
    //
    protected $table = 'rate_agent_jxn';
    protected $guarded = ['id'];

    // public function rates() {
    //     return $this->belongsTo('App\Rate', 'rate_id', 'id');
    // }
}
