<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Paction;
use App\Models\Faction;
use App\Models\User;

class Globalaction extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
    'user_id' ,'action_id', 'discription', 'date',
  ];

  public $timestamps = false;
  
  protected $appends = [
    'UserName'
  ];

  public function user() {
		return $this->belongsTo(User::class);
	}

  public function paction() {
		return $this->hasMany(Paction::class);
	}

  public function faction() {
		return $this->hasMany(Faction::class);
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