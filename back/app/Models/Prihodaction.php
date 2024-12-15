<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Prihod;
use App\Models\User;
use App\Models\People;
use App\Models\Globalaction;

class Prihodaction extends Model
{
  
  protected $hidden = [
  ];

  protected $fillable = [
    'globalaction_id', 'people_id', 'source_id', 'target_id'
  ];

  public $timestamps = false;
  
  protected $appends = [
    'UserName', 'PersoneName', 'SourceItem', 'TargetItem',
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

  public function getUserNameAttribute()
  {
    if ($this->user){
      return $this->user->name;
    } else {
      return 'Не определено';
    }
  }

  public function getPersoneNameAttribute()
  {
    if ($this->people_id){
      $curPersone = DB::table('people')->find($this->people_id);
      return $curPersone->name.' '.$curPersone->first_name.' '.$curPersone->patronymic;
    } else {
      return 'Не определено';
    }
  }

  public function getSourceItemAttribute()
  {
    if ($this->source_id){
      $curPrihod = DB::table('prihods')->find($this->source_id);
      return $curPrihod;
    } else {
      return [];
    }
  }

  public function getTargetItemAttribute()
  {
    if ($this->target_id){
      $curPrihod = DB::table('prihods')->find($this->target_id);
      return $curPrihod;
    } else {
      return [];
    }
  }
}