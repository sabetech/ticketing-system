<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    //
    protected $table = 'stations';

    public function rates() {
        // return $this->hasMany('\App\Rate', '');
    }
}
