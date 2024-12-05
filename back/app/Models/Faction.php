<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Family;
use App\Models\User;
use App\Models\Globalaction;

class Faction extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
    'family_id', 'global_id', 'user_id' ,'action_id', 'name', 'date', 'field', 'value'
  ];

  public $timestamps = false;
  
  protected $appends = [
    'UserName', 'FamilyName'
  ];

  public function family() {
		return $this->belongsTo(Family::class);
	}

  public function user() {
		return $this->belongsTo(User::class);
	}

  public function globalaction() {
		return $this->belongsTo(Globalaction::class);
	}

  public function getFamilyNameAttribute()
  {
    if ($this->family){
      return $this->family->name;
    } else {
      return 'Не определено';
    }
  }

  public function getUserNameAttribute()
  {
    if ($this->user){
      return $this->user->name;
    } else {
      return 'Не определено';
    }
  }
}