<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StationUser extends Model
{
    //
    protected $table = 'stations_user';
    protected $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = ['user_id', 'stations_id'];

    public $timestamps = false;

    public function agent(){
        return $this->belongsTo("\App\Agent", "user_id", "id");
    }

    public function station(){
        return $this->belongsTo("\App\Station", "stations_id", "id");
    }


}
