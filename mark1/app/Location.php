<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
  /**
   * Get all day preference records for the user
   */
  public function locationPreferences()
  {
    return $this->hasMany('App\LocationPreference');
  }
}
