<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Peopleaction;
use App\Models\Familyaction;
use App\Models\Prihodaction;
use App\Models\Serviceaction;
use App\Models\Targetaction;
use App\Models\User;

class Globalaction extends Model
{
  protected $hidden = [
  ];

  protected $fillable = [
    'user_id' ,'action_id', 'name', 'discription', 'date',
  ];

  public $timestamps = false;
  
  protected $appends = [
    'UserName'
  ];

  public function user() {
		return $this->belongsTo(User::class);
	}

  public function peopleaction() {
		return $this->hasMany(Peopleaction::class);
	}

  public function familyaction() {
		return $this->hasMany(Familyaction::class);
	}

  public function prihodaction() {
		return $this->hasMany(Prihodaction::class);
	}

  public function serviceaction() {
		return $this->hasMany(Serviceaction::class);
	}

  public function targetaction() {
		return $this->hasMany(Targetaction::class);
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