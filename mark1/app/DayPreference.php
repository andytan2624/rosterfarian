<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DayPreference extends Model
{
    /**
    Get the user associated with this particular preference
     * @return object
    */
    public function user(): object {
      return $this->hasOne('App\User');
    }
}
