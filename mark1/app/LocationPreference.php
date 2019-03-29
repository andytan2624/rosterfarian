<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationPreference extends Model
{
  /**
   * Get the user associated with this particular preference
   * @return object
   */
  public function user(): object
  {
    return $this->hasOne('App\User');
  }

  /**
   * Get the user associated with this particular preference
   * @return object
   */
  public function location(): object
  {
    return $this->hasOne('App\Location');
  }
}
