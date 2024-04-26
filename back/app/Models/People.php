<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
      'id', 'first_name' ,'second_name', 'patronymic', 'birthday_date', 'baptism_date',
      'death_date', 'image_url', 'live_addres', 'home_phone', 'mobile_phone', 'email', 'created_at', 'updated_at'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */

  // public function user()
  // {
  //    return $this->belongsTo('App\User');
  // }

  // public function club()
  // {
  //    return $this->belongsTo('App\Club');
  // }

  // public function winner()
  // {
  // 	   return $this->belongsTo('App\Winner');
  // }

  // public function gameplayer()
  // {
  // 	   return $this->hasMany('App\Gameplayer');
  // }

  // public function gamefases()
  // {
  // 	   return $this->hasMany('App\Gamevotes');
  // }

  // public function gamevotes()
  // {
  // 	   return $this->hasMany('App\Gamefases');
  // }

  // public function gamefirstshot()
  // {
  // 	   return $this->hasOne('App\Gamefirstshot');
  // }

  // public function getWinnerNameAttribute()
  // {
  //   if ($this->winner){
  //     return $this->winner->name;
  //   } else {
  //     return '';
  //   }
  // }

}
