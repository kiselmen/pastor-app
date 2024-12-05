<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Prihod;
use App\Models\User;
use App\Models\Globalaction;

class Praction extends Model
{
  
  protected $hidden = [
  ];

  protected $fillable = [
    'global_id', 'user_id' ,'action_id', 'name', 'date', 'source_id', 'target_ir'
  ];

  public $timestamps = false;
  
  protected $appends = [
    'UserName', 'PersoneName'
  ];

  public function source() {
		return $this->belongsTo(Prihod::class);
	}

  public function target() {
		return $this->belongsTo(Prihod::class);
	}

  public function user() {
		return $this->belongsTo(User::class);
	}

  public function globalaction() {
		return $this->belongsTo(Globalaction::class);
	}

  public function getSourceNameAttribute()
  {
    if ($this->source){
      return $this->source->name;
    } else {
      return 'Не определено';
    }
  }

  public function getTargetNameAttribute()
  {
    if ($this->target){
      return $this->target->name;
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